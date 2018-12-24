<?php

use Tintin\Loader\Filesystem;

class FilesystemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var
     */
    private $loader;

    public function setUp()
    {
        $this->filesystem = new Filesystem([
          'path' => __DIR__.'/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__.'/cache'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function testFileExists()
    {
        $this->assertTrue(
            $this->filesystem->exists('app')
        );
        $this->assertFalse(
            $this->filesystem->exists('other')
        );
    }

    /**
     * @inheritdoc
     */
    public function testGetFileResolvedPath()
    {
        $this->assertEquals(
            $this->filesystem->getFileResolvedPath('app'),
            __DIR__.'/view/app.tintin.php'
        );
    }

    /**
     * @inheritdoc
     */
    public function testGetCachePath()
    {
        $this->assertEquals(
            $this->filesystem->getCachePath(),
            __DIR__.'/cache'
        );
    }

    /**
     * @inheritdoc
     */
    public function testGetExtension()
    {
        $this->assertEquals(
            $this->filesystem->getExtension(),
            'tintin.php'
        );
    }

    /**
     * @inheritdoc
     */
    public function testGetFileContent()
    {
        $this->assertEquals(
            $this->filesystem->getFileContent('file'),
            'tintin'
        );
    }
}
