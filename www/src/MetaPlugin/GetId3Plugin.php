<?php

namespace App\MetaPlugin;

use App\Entity\LibraryFile;
use App\LibraryFileMetadataBag;
use Psr\Log\LoggerInterface;

class GetId3Plugin implements PluginInterface
{
    const ATTRIBUTE_PREFIX = 'id3tag';
    const LAYER_DIVIDER = '.';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \getID3
     */
    private $getId3;

    /**
     * @var array
     */
    private $blacklist;

    private $extraction = [
        'album' => [
            'id3tag.flac.comments.album',
            'id3tag.flac.VORBIS_COMMENT.comments.album',
            'id3tag.tags.vorbiscomment.album',
            'id3tag.id3v2.TALB.data',
            'id3tag.id3v2.comments.album',
            'id3tag.tags.id3v2.album',
            'id3tag.id3v1.album',
            'id3tag.id3v1.comments.album',
            'id3tag.tags.id3v1.album',
        ],
        'album_artist' => [
            'id3tag.flac.comments.albumartist',
            'id3tag.flac.comments.album artist',
            'id3tag.flac.VORBIS_COMMENT.comments.albumartist',
            'id3tag.flac.VORBIS_COMMENT.comments.album artist',
            'id3tag.tags.vorbiscomment.album artist',
            'id3tag.tags.vorbiscomment.albumartist',
            'id3tag.id3v2.TPE2.data',
        ],
        'artist' => [
            'id3tag.flac.comments.artist',
            'id3tag.flac.VORBIS_COMMENT.comments.artist',
            'id3tag.tags.vorbiscomment.artist',
            'id3tag.riff.WAVE.INFO.IART.data',
            'id3tag.riff.comments.artist',
            'id3tag.tags.riff.artist',
            'id3tag.id3v2.comments.artist',
            'id3tag.tags.id3v2.artist',
            'id3tag.id3v1.artist',
            'id3tag.id3v1.comments.artist',
            'id3tag.tags.id3v1.artist',
        ],
        'band' => [
            'id3tag.flac.comments.band',
            'id3tag.flac.VORBIS_COMMENT.comments.band',
            'id3tag.tags.vorbiscomment.band',
            'id3tag.id3v2.comments.band',
            'id3tag.tags.id3v2.band',
        ],
        'bitrate' => [
            'id3tag.bitrate',
            'id3tag.audio.bitrate',
            'id3tag.audio.streams.bitrate',
            'id3tag.mpeg.audio.bitrate',
            'id3tag.mpeg.audio.VBR_bitrate',
        ],
        'bits_per_sample' => [
            'id3tag.flac.STREAMINFO.bits_per_sample',
            'id3tag.audio.bits_per_sample',
            'id3tag.audio.streams.bits_per_sample',
            'id3tag.riff.audio.bits_per_sample',
        ],
        'bpm' => [
            'id3tag.id3v2.TBPM.data',
            'id3tag.id3v2.comments.bpm',
            'id3tag.tags.id3v2.bpm',
            'id3tag.flac.comments.bpm',
            'id3tag.flac.VORBIS_COMMENT.comments.bpm',
            'id3tag.flac.comments.tempo',
            'id3tag.flac.VORBIS_COMMENT.comments.tempo',
            'id3tag.tags.vorbiscomment.tempo',
            'id3tag.tags.vorbiscomment.bpm',
        ],
        'composer' => [
            'id3tag.id3v2.TCOM.data',
            'id3tag.id3v2.comments.composer',
            'id3tag.tags.id3v2.composer',
        ],
        'content_group_description' => [
            'id3tag.id3v2.TIT1.data',
            'id3tag.id3v2.comments.content_group_description',
            'id3tag.tags.id3v2.content_group_description',
        ],
        'date' => [
            'id3tag.id3v2.TYER.data',
            'id3tag.id3v2.TDRL.data',
            'id3tag.id3v2.comments.year',
            'id3tag.tags.id3v2.year',
            'id3tag.flac.comments.year',
            'id3tag.flac.comments.date',
            'id3tag.flac.comments.release date',
            'id3tag.flac.VORBIS_COMMENT.comments.year',
            'id3tag.flac.VORBIS_COMMENT.comments.date',
            'id3tag.flac.VORBIS_COMMENT.comments.release date',
            'id3tag.tags.vorbiscomment.year',
            'id3tag.tags.vorbiscomment.date',
            'id3tag.tags.vorbiscomment.release date',
            'id3tag.tags.id3v2.release_time',
            'id3tag.id3v1.year',
            'id3tag.id3v1.comments.year',
            'id3tag.id3v2.comments.release_time',
            'id3tag.tags.id3v1.year',
            'id3tag.id3v2.TDRC.data',
            'id3tag.tags.id3v2.recording_time',
            'id3tag.id3v2.comments.recording_time',
        ],
        'ensemble' => [
            'id3tag.flac.comments.ensemble',
            'id3tag.flac.VORBIS_COMMENT.comments.ensemble',
            'id3tag.tags.vorbiscomment.ensemble',
        ],
        'genre' => [
            'id3tag.id3v2.comments.genre',
            'id3tag.tags.id3v2.genre',
            'id3tag.flac.comments.genre',
            'id3tag.flac.VORBIS_COMMENT.comments.genre',
            'id3tag.tags.vorbiscomment.genre',
            'id3tag.riff.WAVE.INFO.IGNR.data',
            'id3tag.riff.comments.genre',
            'id3tag.tags.riff.genre',
            'id3tag.id3v1.genre',
            'id3tag.id3v1.comments.genre',
            'id3tag.tags.id3v1.genre',
        ],
        'initial_key' => [
            'id3tag.id3v2.TKEY.data',
            'id3tag.id3v2.comments.initial_key',
            'id3tag.tags.id3v2.initial_key',
            'id3tag.flac.comments.initialkey',
            'id3tag.flac.comments.initial key',
            'id3tag.flac.VORBIS_COMMENT.comments.initialkey',
            'id3tag.flac.VORBIS_COMMENT.comments.initial key',
            'id3tag.tags.vorbiscomment.initialkey',
            'id3tag.tags.vorbiscomment.initial key',
        ],
        'isrc' => [
            'id3tag.id3v2.TSRC.data',
            'id3tag.id3v2.comments.isrc',
            'id3tag.tags.id3v2.isrc',
            'id3tag.flac.comments.isrc',
            'id3tag.flac.VORBIS_COMMENT.comments.isrc',
            'id3tag.tags.vorbiscomment.isrc',
        ],
        'label' => [
            'id3tag.flac.comments.label',
            'id3tag.flac.VORBIS_COMMENT.comments.label',
            'id3tag.tags.vorbiscomment.label',
        ],
        'mime_type' => [
            'id3tag.mime_type',
        ],
        'organization' => [
            'id3tag.flac.comments.organization',
            'id3tag.flac.VORBIS_COMMENT.comments.organization',
            'id3tag.tags.vorbiscomment.organization',
        ],
        'original_date' => [
            'id3tag.id3v2.TORY.data',
            'id3tag.id3v2.TDOR.data',
            'id3tag.id3v2.comments.original_year',
            'id3tag.id3v2.comments.original_release_time',
            'id3tag.tags.id3v2.original_year',
            'id3tag.tags.id3v2.original_release_time',
            'id3tag.flac.VORBIS_COMMENT.comments.original release year',
            'id3tag.flac.VORBIS_COMMENT.comments.origdate',
            'id3tag.tags.vorbiscomment.original release year',
            'id3tag.tags.vorbiscomment.origdate',
            'id3tag.flac.comments.origdate',
            'id3tag.tags.id3v2.text.OrigDate',
            'id3tag.id3v2.comments.text.OrigDate',
        ],
        'performer' => [
            'id3tag.flac.comments.performer',
            'id3tag.flac.VORBIS_COMMENT.comments.performer',
            'id3tag.tags.vorbiscomment.performer',
        ],
        'playtime' => [
            'id3tag.playtime_seconds',
        ],
        'publisher' => [
            'id3tag.id3v2.TPUB.data',
            'id3tag.id3v2.comments.publisher',
            'id3tag.tags.id3v2.publisher',
        ],
        'rating' => [
            'id3tag.flac.comments.rating',
            'id3tag.tags.vorbiscomment.rating',
        ],
        'remixer' => [
            'id3tag.flac.comments.remixer',
            'id3tag.flac.VORBIS_COMMENT.comments.remixer',
            'id3tag.flac.VORBIS_COMMENT.comments.interpreted, remixed, or otherwise modified by',
            'id3tag.flac.comments.interpreted, remixed, or otherwise modified by',
            'id3tag.tags.vorbiscomment.remixer',
            'id3tag.tags.vorbiscomment.interpreted, remixed, or otherwise modified by',
            'id3tag.id3v2.comments.remixer',
            'id3tag.id3v2.comments.text.REMIXER',
            'id3tag.tags.id3v2.remixer',
            'id3tag.tags.id3v2.text.REMIXER',
        ],
        'sample_rate' => [
            'id3tag.flac.STREAMINFO.sample_rate',
            'id3tag.audio.sample_rate',
            'id3tag.audio.streams.sample_rate',
            'id3tag.mpeg.audio.sample_rate',
            'id3tag.riff.audio.sample_rate',
        ],
        'style' => [
            'id3tag.flac.comments.style',
            'id3tag.flac.VORBIS_COMMENT.comments.style',
            'id3tag.tags.vorbiscomment.style',
        ],
        'title' => [
            'id3tag.id3v2.TIT2.data',
            'id3tag.id3v2.comments.title',
            'id3tag.tags.id3v2.title',
            'id3tag.flac.comments.title',
            'id3tag.flac.VORBIS_COMMENT.comments.title',
            'id3tag.tags.vorbiscomment.title',
            'id3tag.riff.comments.title',
            'id3tag.riff.WAVE.INFO.INAM.data',
            'id3tag.id3v1.title',
            'id3tag.id3v1.comments.title',
            'id3tag.tags.id3v1.title',
            'id3tag.tags.riff.title',
            'id3tag.id3v2.TT2.data',
        ],
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->getId3 = new \getID3();
        $this->getId3->setOption([
            'option_tags_html' => false,
            'option_save_attachments' => false,
        ]);
        $this->blacklist = include __DIR__ . '/../Resources/data/get_id3_blacklist.php';
    }

    public function analyze(LibraryFile $libraryFile, LibraryFileMetadataBag $bag)
    {
        if (!$libraryFile->getIsFileExists()) {
            return;
        }

        $info = $this->getId3->analyze($libraryFile->getLibraryPathName());

        // todo Use warnings and errors
        unset($info['warning']);
        unset($info['error']);

        $this->flatten(self::ATTRIBUTE_PREFIX, $info, $flat);

        foreach ([
            'playtime',
            'bitrate',
            'bpm',
        ] as $attribute) {
            $this->extractValueSingleFloat($attribute, $flat, $bag);
        }

        foreach ([
            'bits_per_sample',
            'sample_rate',
        ] as $attribute) {
            $this->extractValueSingleInt($attribute, $flat, $bag);
        }

        foreach ([
            'album',
            'title',
            'initial_key',
            'isrc',
            'mime_type',
        ] as $attribute) {
            $this->extractValueSingleString($attribute, $flat, $bag);
        }

        foreach ([
            'artist',
            'album_artist',
        ] as $attribute) {
            $this->extractArtist($attribute, $flat, $bag);
        }

        $this->extractGenre($flat, $bag);
        $this->extractAttributeDate($flat, $bag);
        $this->extractAttributeOriginalDate($flat, $bag);
    }

    private function extractValueSingleFloat(string $attribute, array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction[$attribute] as $k) {
            if (!empty($data[$k])) {
                foreach ($data[$k] as $v) {
                    if (is_numeric($v)) {
                        $bag->setValues($attribute, [round((float) $v, 5)]);

                        return;
                    }
                }
            }
        }
    }

    private function extractValueSingleInt(string $attribute, array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction[$attribute] as $k) {
            if (!empty($data[$k])) {
                foreach ($data[$k] as $v) {
                    if (
                        is_int($v)
                        || ctype_digit($v)
                    ) {
                        $bag->setValues($attribute, [(int) $v]);

                        return;
                    }
                }
            }
        }
    }

    private function extractValueSingleString(string $attribute, array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction[$attribute] as $k) {
            if (!empty($data[$k])) {
                foreach ($data[$k] as $v) {
                    if (!empty($v)) {
                        $bag->setValues($attribute, [(string) $v]);

                        return;
                    }
                }
            }
        }
    }

    private function extractArtist(string $attribute, array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction[$attribute] as $k) {
            if (!empty($data[$k])) {
                $chunks = preg_split('/[;,\0]/', implode("\0", $data[$k]));
                $bag->setValues($attribute, array_map('trim', $chunks));

                return;
            }
        }
    }

    private function extractGenre(array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction['genre'] as $k) {
            if (!empty($data[$k])) {
                $chunks = preg_split('/[,\0]/', implode("\0", $data[$k]));
                $bag->setValues('genre', array_map('trim', $chunks));

                return;
            }
        }
    }

    private function extractAttributeDate(array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction['date'] as $k) {
            if (!empty($data[$k])) {
                foreach ($data[$k] as $v) {
                    preg_match('/^(\d{4})(-\d{1,2}-\d{1,2}){0,1}$/', $v, $matches);

                    if (
                        !empty($matches[1])
                        && !$bag->hasValues('year')
                    ) {
                        $bag->setValues('year', [$matches[1]]);
                    }

                    if (
                        !empty($matches[1])
                        && !empty($matches[2])
                        && !$bag->hasValues('date')
                    ) {
                        $bag->setValues('date', [$matches[1] . $matches[2]]);
                    }

                    if (
                        $bag->hasValues('year')
                        && $bag->hasValues('date')
                    ) {
                        return;
                    }
                }
            }
        }
    }

    private function extractAttributeOriginalDate(array &$data, LibraryFileMetadataBag $bag)
    {
        foreach ($this->extraction['original_date'] as $k) {
            if (!empty($data[$k])) {
                foreach ($data[$k] as $v) {
                    preg_match('/^(\d{4})(-\d{1,2}-\d{1,2}){0,1}/', $v, $matches);

                    if (
                        !empty($matches[1])
                        && !$bag->hasValues('year')
                    ) {
                        $bag->setValues('year', [$matches[1]]);
                    }

                    if (
                        !empty($matches[1])
                        && !empty($matches[2])
                        && !$bag->hasValues('date')
                    ) {
                        $bag->setValues('date', [$matches[1] . $matches[2]]);
                    }

                    if (
                        !empty($matches[1])
                        && !$bag->hasValues('original_year')
                    ) {
                        $bag->setValues('original_year', [$matches[1]]);
                    }

                    if (
                        !empty($matches[1])
                        && !empty($matches[2])
                        && !$bag->hasValues('original_date')
                    ) {
                        $bag->setValues('original_date', [$matches[1] . $matches[2]]);
                    }

                    if (
                        $bag->hasValues('date')
                        && $bag->hasValues('year')
                        && $bag->hasValues('original_date')
                        && $bag->hasValues('original_year')
                    ) {
                        return;
                    }
                }
            }
        }
    }

    private function flatten(string $layer, $details, &$data)
    {
        if (
            $details === null
            || in_array($layer, $this->blacklist)
            || !mb_check_encoding($layer, 'utf-8')
        ) {
            return;
        }

        if (is_scalar($details)) {
            if (
                $details !== ''
                && mb_check_encoding($details, 'utf-8')
                && (
                    !isset($data[$layer])
                    || !in_array($details, $data[$layer])
                )
            ) {
                $data[$layer][] = $details;
            }
        } elseif(is_array($details)) {
            foreach ($details as $subLayer => $subDetails) {
                if (is_int($subLayer)) {
                    $layerName = $layer;
                } else {
                    $layerName = $layer . self::LAYER_DIVIDER . $subLayer;
                }

                if (!in_array($layerName, $this->blacklist)) {
                    $this->flatten($layerName, $subDetails, $data);
                }
            }
        } else {
            throw new \UnexpectedValueException(sprintf('Unexpected type "%s" in layer "%s"', gettype($details), $layer));
        }
    }
}
