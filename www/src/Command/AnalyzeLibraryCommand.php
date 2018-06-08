<?php

namespace App\Command;

use App\Entity\Library;
use App\Entity\LibraryFile;
use App\Entity\LibraryFileAttribute;
use App\Entity\LibraryFileAttributeValue;
use App\LibraryFileAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class AnalyzeLibraryCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var LibraryFileAnalyzer
     */
    private $libraryFileAnalyzer;

    /**
     * @var LibraryFileAttribute[]
     */
    private $staticAttributes = [];

    /**
     * @var LibraryFileAttribute[]
     */
    private $attributes = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        Filesystem $filesystem,
        LoggerInterface $logger,
        LibraryFileAnalyzer $libraryFileAnalyzer
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
        $this->libraryFileAnalyzer = $libraryFileAnalyzer;
    }

    protected function configure()
    {
        $this
            ->setName('app:library:analyze')
            ->setDescription('Analyzes library files and saves their metadata.')
            ->addOption('lib_id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
            ->addOption('lib_file_id', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        /** @var LibraryFileAttribute $a */
        foreach ($this->entityManager->getRepository(LibraryFileAttribute::class)->findAll() as $a) {
            $this->attributes[$a->getName()] = $a;

            if ($a->getIsStatic()) {
                $this->staticAttributes[] = $a;
            }
        }

        $libraryIds = $input->getOption('lib_id');
        $libraryFileIds = $input->getOption('lib_file_id');
        $libraryRepo = $this->entityManager->getRepository(Library::class);

        $libraryFiles = [];
        foreach ($libraryFileIds as $libraryFileId) {
            $libraryFile = $this->entityManager->getRepository(LibraryFile::class)->find($libraryFileId);

            if (!$libraryFile) {
                throw new \RuntimeException(sprintf('Library file with ID "%s" not found', $libraryFileId));
            }

            $libraryFiles[] = $libraryFile;
        }

        $libraries = [];
        foreach ($libraryIds as $libraryId) {
            $library = $libraryRepo->find($libraryId);

            if (!$library) {
                throw new \RuntimeException(sprintf('Library with ID "%s" not found', $libraryId));
            }

            $libraries[] = $library;
        }

        if (count($libraries) === 0 && count($libraryFiles) === 0) {
            $libraries = $libraryRepo->findAll();
        }

        foreach ($libraryFiles as $libraryFile) {
            $this->analyzeLibraryFile($libraryFile);
        }

        foreach ($libraries as $library) {
            $this->analyzeLibrary($library);
        }
    }

    private function analyzeLibrary(Library $library)
    {
        if (!$this->filesystem->exists($library->getPath())) {
            $this->logger->error(sprintf('Path "%s" of library "%s" does not exist; skipping library analysis.', $library->getPath(), $library->getName()));

            return;
        }

        $this->logger->info(sprintf('Analyzing library "%s"', $library->getName()));

        foreach ($library->getLibraryFiles() as $libraryFile) {
            $this->analyzeLibraryFile($libraryFile);
        }
    }

    private function analyzeLibraryFile(LibraryFile $libraryFile)
    {
        $this->logger->debug(sprintf('Analyzing data for file "%s" of library "%s"', $libraryFile->getPathName(), $libraryFile->getLibrary()->getName()));

        if (!$this->filesystem->exists($libraryFile->getLibraryPathName())) {
            $libraryFile->setIsFileExists(false);
            $this->entityManager->flush();

            return;
        }

        $libraryFile->setAnalyzedAt(new \DateTime());
        $bag = $this->libraryFileAnalyzer->analyze($libraryFile);
        $providedValues = $bag->getAll();
        $providedAttributes = [];

        // Update values of existing attributes
        foreach ($providedValues as $attributeName => $values) {
            if (!array_key_exists($attributeName, $this->attributes)) {
                $this->logger->info(sprintf('Skipping unknown attribute "%s"', $attributeName));
                continue;
            }

            $providedAttributes[] = $this->attributes[$attributeName];

            /** @var LibraryFileAttributeValue[] $existingValues */
            $existingValues = $this
                ->entityManager
                ->getRepository(LibraryFileAttributeValue::class)
                ->findBy([
                    'libraryFile' => $libraryFile,
                    'libraryFileAttribute' => $this->attributes[$attributeName],
                ])
            ;

            foreach ($existingValues as $k => $v) {
                switch ($this->attributes[$attributeName]->getType()) {
                    case LibraryFileAttribute::TYPE_BOOL:
                        $actualV = $v->getValueBool();
                        break;

                    case LibraryFileAttribute::TYPE_DATE:
                        $actualV = $v->getValueDate()->format('Y-m-d');
                        break;

                    case LibraryFileAttribute::TYPE_DATE_TIME:
                        $actualV = $v->getValueDate()->format('Y-m-d H:i:s');
                        break;

                    case LibraryFileAttribute::TYPE_FLOAT:
                        $actualV = $v->getValueFloat();
                        break;

                    case LibraryFileAttribute::TYPE_INT:
                        $actualV = $v->getValueInt();
                        break;

                    case LibraryFileAttribute::TYPE_STRING:
                        $actualV = $v->getValueString();
                        break;

                    default:
                        $actualV = $v->getValue();
                }

                while (($index = array_search($actualV, $values)) !== false) {
                    unset($values[$index]);
                    unset($existingValues[$k]);
                }
            }

            foreach ($existingValues as $v) {
                $this->entityManager->remove($v);
            }

            foreach ($values as $v) {
                $libraryFileAttributeValue = new LibraryFileAttributeValue();
                $libraryFileAttributeValue
                    ->setLibraryFile($libraryFile)
                    ->setLibraryFileAttribute($this->attributes[$attributeName])
                ;

                switch ($this->attributes[$attributeName]->getType()) {
                    case LibraryFileAttribute::TYPE_BOOL:
                        $libraryFileAttributeValue->setValueBool($v);
                        break;

                    case LibraryFileAttribute::TYPE_DATE:
                        $libraryFileAttributeValue->setValueDate(new \DateTime($v));
                        break;

                    case LibraryFileAttribute::TYPE_DATE_TIME:
                        $libraryFileAttributeValue->setValueDateTime(new \DateTime($v));
                        break;

                    case LibraryFileAttribute::TYPE_FLOAT:
                        $libraryFileAttributeValue->setValueFloat($v);
                        break;

                    case LibraryFileAttribute::TYPE_INT:
                        $libraryFileAttributeValue->setValueInt($v);
                        break;

                    case LibraryFileAttribute::TYPE_STRING:
                        $libraryFileAttributeValue->setValueString($v);
                        break;

                    default:
                        $libraryFileAttributeValue->setValue($v);
                }

                $this->entityManager->persist($libraryFileAttributeValue);
            }
        }

        // Drop values of non-provided attributes
        $deleteQb = $this
            ->entityManager
            ->createQueryBuilder()
            ->delete(LibraryFileAttributeValue::class, 'lfav')
            ->where('lfav.libraryFile = :library_file_id')
            ->setParameter('library_file_id', $libraryFile->getId())
        ;

        if (count($this->staticAttributes) > 0) {
            $deleteQb
                ->andWhere('lfav.libraryFileAttribute NOT IN (:static_attributes)')
                ->setParameter('static_attributes', $this->staticAttributes)
            ;
        }

        if (count($providedAttributes) > 0) {
            $deleteQb
                ->andWhere('lfav.libraryFileAttribute NOT IN (:provided_attributes)')
                ->setParameter('provided_attributes', $providedAttributes)
            ;
        }

        $deleteQb
            ->getQuery()
            ->execute()
        ;

        $this->entityManager->flush();
        $this->entityManager->clear(LibraryFileAttributeValue::class);
    }
}
