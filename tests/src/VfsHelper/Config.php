<?php

declare(strict_types=1);

namespace Lazer\Test\VfsHelper;

use org\bovigo\vfs\vfsStream;

trait Config {

    protected $root;

    protected function setUpFilesystem()
    {
        $this->root = vfsStream::setup('data');
        vfsStream::copyFromFileSystem(ROOT . 'tests/db');
    }
}
