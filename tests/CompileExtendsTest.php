<?php

use Tintin\Compiler;

class CompileExtendsTest extends \PHPUnit\Framework\TestCase
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

    /**
     * Test #While statement
     */
    public function testCompileInclude()
    {
        $compile_include = $this->makeReflectionFor('compileInclude');

        $render = $compile_include->invoke(new Compiler, 'include', ['name' => 'Tintin']);

        $this->assertEquals('Tintin', $render);
    }
}
