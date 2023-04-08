<?php

use Tintin\Tintin;
use Tintin\Loader\Filesystem;

class TintinTest extends \PHPUnit\Framework\TestCase
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
        $this->assertInstanceOf(Filesystem::class, $this->loader);
        
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
     * Test simple rendering
     * 
     * @dataProvider getComputeData
     */
    public function testRenderSimpleDataCompute(int $value, string $sign, int $result)
    {
        $tintin = new Tintin;

        $render = $tintin->render('{{{ $value ' . $sign . ' $value }}}', compact('value'));

        $this->assertEquals($render, $result);
    }
    
    /**
     * Test false custom directive rendering
     */
    public function testRenderFalseDirective()
    {
        $tintin = new Tintin;

        $render = $tintin->render('%falseDirective <href="%link">');

        $this->assertEquals($render, '%falseDirective <href="%link">');
    }

    /**
     * The compute dataset
     * 
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
