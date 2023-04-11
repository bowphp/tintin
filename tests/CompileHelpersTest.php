<?php

use Tintin\Compiler;

class CompileHelpersTest extends \PHPUnit\Framework\TestCase
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

    public function testCompileAuthStatement()
    {
        $compile_if = $this->makeReflectionFor('compileAuth');

        $render = $compile_if->invoke($this->compiler, '%auth');
        $this->assertEquals($render, '<?php if (auth()->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%auth ');
        $this->assertEquals($render, '<?php if (auth()->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%auth("admin")');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%auth ("admin")');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?>');
    }

    public function testcompileEndAuthStatement()
    {
        $compile_else = $this->makeReflectionFor('compileEndAuth');

        $render = $compile_else->invoke(new Compiler(), '%endauth');
        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__ . '/view/auth.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if (auth()->check()): ?>', $render);
    }
}
