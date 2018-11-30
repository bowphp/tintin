<?php

use Tintin\Tintin;

class CompileCustomDirectiveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tintin
     */
    private $tintin;

    public function setUp()
    {
        $this->tintin = new Tintin;
    }

    public function testHelloDirective()
    {
        $this->tintin->directive('hello', function (array $attributes = []) {
            return 'Hello ' . implode(" ", $attributes);
        });

        $r = $this->tintin->render('#hello("Tintin", "Bow")');

        $this->assertEquals($r, "Hello Tintin Bow");
    }

    public function testSimpleDirective()
    {
        $this->tintin->directive('now', function (array $attributes = []) {
            return time();
        });

        $r = $this->tintin->render('#now');

        $this->assertTrue(is_numeric($r));
    }

    public function testComplexDirective()
    {
        $this->tintin->directive('input', function (array $attributes = []) {
            $attribute = $attributes[0];

            return '<input type="'.$attribute['type'].'" name="'.$attribute['name'].'" value="'.$attribute['value'].'" />';
        });

        $r = $this->tintin->render('#input(["type" => "text", "value" => null, "name" => "name"])');

        $this->assertEquals($r, '<input type="text" name="name" value="" />');
    }
}
