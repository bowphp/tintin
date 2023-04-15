<?php

use Tintin\Compiler;
use CompileClassReflection;

class CompileMacroTest extends PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    private Compiler $compiler;
    
    public function setUp(): void
    {
        $this->compiler = new Compiler();
    }

    public function testCompileMacro()
    {
        $template = file_get_contents(__DIR__.'/view/macro.tintin.php');
        $compileMacro = $this->makeReflectionFor('compileMacro');
        $output = $compileMacro->invoke($this->compiler, $template);

        $this->assertStringContainsString("Hello {{ \$name }}", $output);
    }
}