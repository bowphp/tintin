<?php

use Tintin\Compiler;

class CompileClassTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    /**
     * @var Compiler
     */
    private Compiler $compiler;

    public function setUp(): void
    {
        $this->compiler = new Compiler();
    }

    public function testCompileClass()
    {
        $compile_class = $this->makeReflectionFor('compileClass');

        $render = $compile_class->invoke($this->compiler, "%class(['bg-red', 'text-dark'])");

        $this->assertEquals($render, "<?= 'class=\"bg-red text-dark\"' ?>");
    }

    public function testCompileClassWithCondition()
    {
        $compile_class = $this->makeReflectionFor('compileClass');

        $isActive = true;

        $render = $compile_class->invoke($this->compiler, "%class (['bg-red', 'text-dark', 'underline' => ! $isActive, 'font-bold' => $isActive])");

        $this->assertEquals($render, "<?= 'class=\"bg-red text-dark font-bold\"' ?>");
    }
}
