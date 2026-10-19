<?php

use Tintin\Filesystem;

class FilesystemTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function setUp(): void
    {
        $this->filesystem = new Filesystem([
          'path' => __DIR__ . '/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__ . '/cache'
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
            __DIR__ . '/view/app.tintin.php'
        );
    }

    /**
     * @inheritdoc
     */
    public function testGetCachePath()
    {
        $this->assertEquals(
            $this->filesystem->getCachePath(),
            __DIR__ . '/cache'
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

    /**
     * Path resolution must stay inside the configured view directory and
     * never resolve a file outside it via traversal sequences.
     */
    public function testGetFileResolvedPathRejectsTraversal()
    {
        $this->assertFalse(
            $this->filesystem->getFileResolvedPath('../../../../../../etc/passwd')
        );
        $this->assertFalse(
            $this->filesystem->exists('../../../../../../etc/passwd')
        );
    }

    /**
     * Build a filesystem over a scratch directory holding one template, so
     * the fixtures' timestamps are never touched.
     *
     * @return array{0: Filesystem, 1: string, 2: string} filesystem, template path, cache path
     */
    private function makeScratchFilesystem(): array
    {
        $root = sys_get_temp_dir() . '/tintin_fs_' . uniqid();
        mkdir($root . '/view', 0777, true);
        mkdir($root . '/cache', 0777, true);

        $template = $root . '/view/probe.tintin.php';
        file_put_contents($template, 'Hello {{ $name }}');

        $filesystem = new Filesystem([
            'path' => $root . '/view',
            'extension' => 'tintin.php',
            'cache' => $root . '/cache',
        ]);

        $filesystem->cache('probe', '<?php echo "compiled"; ?>');

        return [$filesystem, $template, $filesystem->getCacheFileResolvedPath('probe')];
    }

    /**
     * A template saved again in the same second it was compiled (mtimes are
     * whole seconds) must be compiled again, not served stale forever.
     */
    public function testIsExpiredWhenTemplateEditedInTheSameSecondAsItsCache()
    {
        [$filesystem, $template, $cache] = $this->makeScratchFilesystem();

        $now = time();
        touch($template, $now);
        touch($cache, $now);
        clearstatcache();

        $this->assertTrue($filesystem->isExpired('probe'));
    }

    public function testIsExpiredFollowsTheTemplateMtime()
    {
        [$filesystem, $template, $cache] = $this->makeScratchFilesystem();

        touch($template, time() - 100);
        touch($cache, time() - 50);
        clearstatcache();
        $this->assertFalse($filesystem->isExpired('probe'), 'cache newer than the template is fresh');

        touch($template, time() - 10);
        clearstatcache();
        $this->assertTrue($filesystem->isExpired('probe'), 'template saved after the cache is stale');
    }

    /**
     * Rendering reads the cache and moves its atime; that must not count as
     * the cache being "newer" than a template edited afterwards.
     */
    public function testIsExpiredIgnoresTheCacheAccessTime()
    {
        [$filesystem, $template, $cache] = $this->makeScratchFilesystem();

        touch($cache, time() - 100, time() + 3600); // old mtime, atime in the future
        touch($template, time() - 50);
        clearstatcache();

        $this->assertTrue($filesystem->isExpired('probe'));
    }

    public function testIsExpiredWhenNothingIsCached()
    {
        [$filesystem, , $cache] = $this->makeScratchFilesystem();

        unlink($cache);
        clearstatcache();

        $this->assertTrue($filesystem->isExpired('probe'));
    }
}
