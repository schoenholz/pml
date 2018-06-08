<?php

namespace App;

use App\Entity\LibraryFile;
use App\MetaPlugin\GetId3Plugin;
use App\MetaPlugin\PluginInterface;

class LibraryFileAnalyzer
{
    /**
     * @var PluginInterface[]
     */
    private $metaPlugins;

    public function __construct(GetId3Plugin $getId3Plugin)
    {
        $this->metaPlugins = [
            $getId3Plugin,
        ];
    }

    public function analyze(LibraryFile $libraryFile): LibraryFileMetadataBag
    {
        $bag = new LibraryFileMetadataBag();

        foreach ($this->metaPlugins as $plugin) {
            $plugin->analyze($libraryFile, $bag);
        }

        return $bag;
    }
}
