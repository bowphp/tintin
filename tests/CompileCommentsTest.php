<?php

use Tintin\Compiler;

class CompileCommentsTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;
    
    /**
     * @var Compiler
     */
    private Compiler $compiler;

    public function setUp(): void
    {
        $this->compiler = new Compiler;
    }

    public function testComment()
    {
        $comment = $this->makeReflectionFor('compileComments');

        $render = $comment->invoke(new Compiler, '{ Hello, world #}');

        $this->assertNotEquals($render, "");

        $render = $comment->invoke(new Compiler, '{# Hello, world #}');

        $this->assertEquals($render, "");
    }
}
