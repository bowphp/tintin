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

        $render = $compile_if->invoke($this->compiler, '%auth ("admin") ');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?> ');
    }

    public function testCompileGuestStatement()
    {
        $compile_if = $this->makeReflectionFor('compileGuest');

        $render = $compile_if->invoke($this->compiler, '%guest');
        $this->assertEquals($render, '<?php if (!auth()->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%guest ');
        $this->assertEquals($render, '<?php if (!auth()->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%guest("admin")');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%guest ("admin")');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?>');

        $render = $compile_if->invoke($this->compiler, '%guest ("admin") ');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?> ');
    }

    public function testCompileLangStatement()
    {
        $compile_if = $this->makeReflectionFor('compileLang');

        $render = $compile_if->invoke($this->compiler, '%lang("fr")');
        $this->assertEquals($render, '<?php if (client_locale() == "fr"): ?>');

        $render = $compile_if->invoke($this->compiler, '%lang("fr") ');
        $this->assertEquals($render, '<?php if (client_locale() == "fr"): ?> ');
    }

    public function testCompileEnvStatement()
    {
        $compile_if = $this->makeReflectionFor('compileEnv');

        $render = $compile_if->invoke($this->compiler, '%env("production")');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?>');

        $render = $compile_if->invoke($this->compiler, '%env("production") ');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?> ');
    }

    public function testCompileProductionStatement()
    {
        $compile_if = $this->makeReflectionFor('compileProduction');

        $render = $compile_if->invoke($this->compiler, '%production');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?>');

        $render = $compile_if->invoke($this->compiler, '%production("hello world")');
        $this->assertEquals($render, '<?php throw new \ErrorException("The %production cannot take the parameters!") ?>');
    }

    public function testcompileEndHelpersStatement()
    {
        $compile_else = $this->makeReflectionFor('compileEndHelpers');

        $render = $compile_else->invoke($this->compiler, '%endauth');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endguest');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endlang');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endenv');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endproduction');
        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__ . '/view/auth.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if (auth()->check()): ?>', $render);
    }
}
