<?php

use Tintin\Tintin;
use Tintin\Filesystem;

class TintinWithFilesystemLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $loader;

    /**
     * @var Tintin
     */
    private Tintin $instance;

    public function setUp(): void
    {
        $this->loader = new Filesystem([
          'path' => __DIR__ . '/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__ . '/cache'
        ]);
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertInstanceOf(Filesystem::class, $this->loader);

        $instance = new Tintin($this->loader);

        $this->assertInstanceOf(Tintin::class, $instance);
    }

    /**
     * Test simple rendering 2
     */
    public function testRender()
    {
        $tintin = new Tintin($this->loader);

        $render = $tintin->render('app', ['name' => "Tintin"]);

        $this->assertTrue((bool) preg_match('/Tintin/', $render));
    }

    /**
     * Test extends inherits
     */
    public function testRenderExtends()
    {
        $tintin = new Tintin($this->loader);

        $render = $tintin->render('child');

        $this->assertTrue((bool) preg_match('/<p>(.+?)<\/p>/', $render));
    }
}
