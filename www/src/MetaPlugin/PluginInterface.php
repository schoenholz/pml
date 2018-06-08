<?php

namespace App\MetaPlugin;

use App\Entity\LibraryFile;
use App\LibraryFileMetadataBag;

interface PluginInterface
{
    public function analyze(LibraryFile $libraryFile, LibraryFileMetadataBag $bag);
}
