<?php

namespace App\Command;

use App\Entity\Library;
use App\Entity\LibraryFile;
use App\Repository\LibraryFileRepository;
use App\Repository\LibraryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ScanLibraryCommand extends Command
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

    public function __construct(
        EntityManagerInterface $entityManager,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    protected function configure()
    {
        $this
            ->setName('app:library:scan')
            ->setDescription('Scans libraries for files.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        /** @var LibraryRepository $libraryRepo */
        $libraryRepo = $this->entityManager->getRepository(Library::class);
        /** @var LibraryFileRepository $libraryFileRepo */
        $libraryFileRepo = $this->entityManager->getRepository(LibraryFile::class);

        foreach ($libraryRepo->findAll() as $library) {
            $this->logger->info(sprintf('Scanning library "%s"', $library->getName()));

            if (!$this->filesystem->exists($library->getPath())) {
                $this->logger->error(sprintf('Path "%s" of library "%s" not found; skipping scan.', $library->getPath(), $library->getName()));
                continue;
            }

            $finder = new Finder();
            $files = $finder
                ->files()
                // todo Move file extensions to configuration
                // todo Add command to list not matching files
                ->name('/\.(?:aiff|alac|flac|mp3|wav|wma)$/i')
                ->in($library->getPath())
            ;

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $this->logger->debug(sprintf('Found file "%s" in library "%s"', $file->getRelativePathname(), $library->getName()));
                $libraryFile = $libraryFileRepo->findOneBy([
                    'library' => $library,
                    'path' => $file->getRelativePath(),
                    'name' => $file->getBasename(),
                ]);

                if (!$libraryFile) {
                    $libraryFile = new LibraryFile();
                    $libraryFile
                        ->setLibrary($library)
                        ->setName($file->getBasename())
                        ->setPath($file->getRelativePath())
                    ;
                    $this->entityManager->persist($libraryFile);
                }

                $libraryFile
                    ->setScannedAt(new \DateTime())
                    ->setIsFileExists(true)
                    ->setFileCreatedAt((new \DateTime())->setTimestamp(filectime($file->getPathname())))
                    ->setFileModifiedAt((new \DateTime())->setTimestamp(filemtime($file->getPathname())))
                    ->setFileSize(filesize($file->getPathname()))
                ;

                $this->entityManager->flush();
                $this->entityManager->clear(LibraryFile::class);
            }
        }
    }
}
