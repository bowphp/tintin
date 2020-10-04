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
     * Test simple rendering 1
     */
    public function testRenderSimpleData()
    {
        $tintin = new Tintin;

        $render = $tintin->render('{{ $name }}', ['name' => "Tintin"]);

        $this->assertEquals($render, 'Tintin');
    }

    /**
     * Test simple rendering 1
     * @depends getComputeData
     */
    public function testRenderSimpleDataCompute($data)
    {
        $tintin = new Tintin;

        $render1 = $tintin->render('{{{ $num + $num }}}', ['num' => $data['value'], 'sign' => $data['sign']]);

        $this->assertEquals($render1, $data['result']);
    }
    
    /**
     * Test false custom directive rendering
     */
    public function testRenderFalseDirective()
    {
        $tintin = new Tintin;

        $render = $tintin->render('#falseDirective <href="#link">');

        $this->assertEquals($render, '#falseDirective <href="#link">');
    }

    /**
     * The compute dataset
     * @return array
     */
    public function getComputeData()
    {
        return [
            ['value' => 5, 'sign' => '+', 'result' => 10],
            ['value' => 5, 'sign' => '-', 'result' => 0],
            ['value' => 5, 'sign' => '*', 'result' => 25],
            ['value' => 5, 'sign' => '/', 'result' => 1],
        ];
    }
}
