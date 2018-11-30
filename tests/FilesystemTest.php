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
    public function testGetFileResolvedPath()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testGetCacheFileResolvedPath()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testGetCachePath()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testGetExtension()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testGetFileContent()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testIsExpirated()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testIsCached()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testFileExists()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function testCache()
    {
        //
    }
}
