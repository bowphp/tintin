<?php

use Tintin\Compiler;

class CompileCommentsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Compiler
     */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new Compiler;
    }

    /**
     * Reflection maker
     *
     * @param string $method
     */
    public function makeReflectionFor($method)
    {
        $reflection = new \ReflectionMethod('\Tintin\Compiler', $method);
    
        $reflection->setAccessible(true);

        return $reflection;
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
