<?php

use Tintin\Tintin;
use Tintin\Loader\Filesystem;

class TintinWithFilesystemLoaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var
     */
    private $loader;

    /**
     * @var Tintin
     */
    private $instance;

    public function setUp()
    {
        $this->loader = new Filesystem([
          'path' => __DIR__.'/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__.'/cache'
        ]);
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertInstanceOf(\Tintin\Loader\Filesystem::class, $this->loader);
        
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
