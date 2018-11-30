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
        $this->tintin->directive('hello', function(array $attributes = []) {
            return 'Hello ' . implode(" ", $attributes);
        });

        $r = $this->tintin->render('#hello("Tintin", "Bow")');

        $this->assertEquals($r, "Hello Tintin Bow");
    }
}
