<?php

namespace App\Command;

use App\Entity\LibraryFile;
use App\Entity\LibraryFileAttribute;
use App\Entity\Playlist;
use App\Entity\PlaylistEntry;
use App\Exception\FilterQuerySemanticException;
use App\FilterQuery\FilterQueryBuilder;
use App\FilterQuery\FilterQueryLexer;
use App\Repository\LibraryFileRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildPlaylistCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FilterQueryLexer
     */
    private $filterQueryLexer;

    /**
     * @var FilterQueryBuilder
     */
    private $filterQueryBuilder;

    /**
     * @var LibraryFileAttribute[]
     */
    private $libraryFileAttributes = [];

    //public function __construct(EntityManagerInterface $entityManager, FilterQueryLexer $filterQueryLexer, FilterQueryBuilder $filterQueryBuilder)
    //{
    //    parent::__construct();
    //
    //    $this->entityManager = $entityManager;
    //    $this->filterQueryLexer = $filterQueryLexer;
    //    $this->filterQueryBuilder = $filterQueryBuilder;
    //}

    protected function configure()
    {
        $this
            ->setName('app:playlist:build')
            ->setDescription('Builds playlists.')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        /** @var LibraryFileAttribute $a */
        foreach ($this->entityManager->getRepository(LibraryFileAttribute::class)->findAll() as $a) {
            $this->libraryFileAttributes[$a->getName()] = $a;
        }

        /** @var LibraryFileRepository $libraryFileRepo */
        $libraryFileRepo = $this->entityManager->getRepository(LibraryFile::class);
        /** @var PlaylistRepository $libraryRepo */
        $playlistRepo = $this->entityManager->getRepository(Playlist::class);

        /** @var Playlist $playlist */
        $playlist = $playlistRepo->findOneBy([
            'name' => 'test',
        ]);

        if (!$playlist) {
            throw new \RuntimeException('Playlist not found');
        }

        $filter = 'genre = Hardcore & artist = Angerfist';
        $bucket = $this->filterQueryLexer->parse($filter);
        $this->filterQueryBuilder->buildQueryParts($bucket);

        // todo Add conditions for static files.
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('lf.id')
            ->from(LibraryFile::class, 'lf')
        ;

        $qb->where(implode(PHP_EOL, $this->filterQueryBuilder->getDqlParts()));
        $qb->setParameters($this->filterQueryBuilder->getParameters());
        $q = $qb->getQuery();

        var_dump($q->getArrayResult());

        return;
        //$libraryFiles = $this->findLibraryFiles($playlist, $filters);
        //
        //// todo Throwing all aways is for N00bs
        //$qb = $this->entityManager->createQueryBuilder();
        //$qb
        //    ->delete(PlaylistEntry::class, 'pe')
        //    ->where(
        //        'pe.playlist = :playlist_id',
        //        'pe.state = :state'
        //    )
        //    ->setParameter('playlist_id', $playlist->getId())
        //    ->setParameter('state', PlaylistEntry::STATE_AUTO)
        //    ->getQuery()
        //    ->execute()
        //;
        //
        //foreach ($libraryFiles as $libraryFile) {
        //    $playlistEntry = new PlaylistEntry();
        //    $playlistEntry
        //        ->setLibraryFile($libraryFileRepo->find($libraryFile['library_file_id']))
        //        ->setPlaylist($playlist)
        //        ->setState(0)
        //        ->setPosition($libraryFile['position'])
        //    ;
        //    $this->entityManager->persist($playlistEntry);
        //}
        //
        //$this->entityManager->flush();
        //$this->entityManager->clear(LibraryFile::class);
        //$this->entityManager->clear(PlaylistEntry::class);
    }

    //private function findLibraryFiles(Playlist $playlist, array $filters): array
    //{
    //    $qb = $this->entityManager->createQueryBuilder();
    //    $qb
    //        ->select('lf.id', 'pe.position')
    //        ->from(PlaylistEntry::class, 'pe')
    //        ->join('pe.libraryFile', 'lf')
    //        ->where(
    //            'pe.state <> :state',
    //            'pe.playlist = :playlist_id'
    //        )
    //        ->setParameter('state', PlaylistEntry::STATE_AUTO)
    //        ->setParameter('playlist_id', $playlist->getId())
    //    ;
    //
    //    $staticLibraryFiles = $qb->getQuery()->getScalarResult();
    //    $inUsePositions = array_column($staticLibraryFiles, 'position');
    //    $inUseLibraryFiles = array_column($staticLibraryFiles, 'id');
    //
    //    $playlistPosition = 0;
    //    $libraryFiles = [];
    //    foreach ($this->findDynamicLibraryFiles($filters) as $dynamicLibraryFile) {
    //        if (!in_array($dynamicLibraryFile['id'], $inUseLibraryFiles)) {
    //            do {
    //                $playlistPosition ++;
    //            } while (in_array($playlistPosition, $inUsePositions));
    //
    //            $libraryFiles[] = [
    //                'library_file_id' => $dynamicLibraryFile['id'],
    //                'position' => $playlistPosition,
    //            ];
    //        }
    //    }
    //
    //    return $libraryFiles;
    //}

    //private function findDynamicLibraryFiles(array $filters): array
    //{
    //    $qb = $this->entityManager->createQueryBuilder();
    //
    //    $qb
    //        ->select('lf.id')
    //        ->from(LibraryFile::class, 'lf')
    //    ;
    //
    //    $i = 0;
    //    foreach ($filters as $filter) {
    //        if (!array_key_exists($filter['attribute'], $this->libraryFileAttributes)) {
    //            throw new \RuntimeException(sprintf('Unknown attribute "%s"', $filter['attribute']));
    //        }
    //
    //        $qb
    //            ->andWhere(sprintf('EXISTS (
    //                SELECT 1
    //                FROM App\Entity\LibraryFileAttributeValue lfav_%d
    //                WHERE
    //                    lfav_%d.libraryFile = lf.id
    //                    AND lfav_%d.libraryFileAttribute = :attribute_%d
    //                    AND lfav_%d.%s = :filter_value_%d
    //            )', $i, $i, $i, $i, $i, $this->getLibraryFileAttributeValueFieldName($filter['attribute']), $i))
    //            ->setParameter(sprintf('attribute_%d', $i), $this->libraryFileAttributes[$filter['attribute']])
    //            ->setParameter(sprintf('filter_value_%d', $i), $filter['value'])
    //        ;
    //        $i ++;
    //    }
    //
    //    // todo Include manually added files here to include them in sorting ($filter['sort']).
    //
    //    return $qb->getQuery()->getScalarResult();
    //}
    //
    //private function getLibraryFileAttributeValueFieldName(string $attributeName): string
    //{
    //    switch ($this->libraryFileAttributes[$attributeName]->getType()) {
    //        case LibraryFileAttribute::TYPE_BOOL:
    //            return'valueBool';
    //
    //        case LibraryFileAttribute::TYPE_DATE:
    //            return'valueDate';
    //
    //        case LibraryFileAttribute::TYPE_DATE_TIME:
    //            return'valueDateTime';
    //
    //        case LibraryFileAttribute::TYPE_FLOAT:
    //            return'valueFloat';
    //
    //        case LibraryFileAttribute::TYPE_INT:
    //            return'valueInt';
    //
    //        case LibraryFileAttribute::TYPE_STRING:
    //            return'valueString';
    //
    //        default:
    //            throw new \RuntimeException(sprintf('Unknown attribute type "%s"', $this->libraryFileAttributes[$attributeName]->getType()));
    //    }
    //}
}
