<?php

use Tintin\Tintin;
use Tintin\Loader\Filesystem;

class TintinTest extends \PHPUnit\Framework\TestCase
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
          'cache' => sys_get_temp_dir()
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
     * Test simplate rendering 1
     */
    public function testRenderSimpeData()
    {
        $tintin = new Tintin;

        $render = $tintin->render('{{ $name }}', ['name' => "Tintin"]);

        $this->assertEquals($render, 'Tintin');
    }

    /**
     * Test simplate rendering 1
     */
    public function testRenderSimpeDataCalcule()
    {
        $tintin = new Tintin;

        $render1 = $tintin->render('{{{ $num + $num }}}', ['num' => 5]);

        $render2 = $tintin->render('{{{ $num - $num }}}', ['num' => 5]);

        $render3 = $tintin->render('{{{ $num * $num }}}', ['num' => 5]);

        $render4 = $tintin->render('{{{ $num / $num }}}', ['num' => 5]);

        $this->assertEquals($render1, "10");
        $this->assertEquals($render2, "0");
        $this->assertEquals($render3, "25");
        $this->assertEquals($render4, "1");
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
     * Test simple rendering 2
     */
    public function testRenderExtends()
    {
        $tintin = new Tintin($this->loader);

        $render = $tintin->render('child');

        var_dump($render);

        $this->assertTrue((bool) preg_match('/<p>(.+?)<\/p>/', $render));
    }
}
