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
        $compileAuth = $this->makeReflectionFor('compileAuth');

        $render = $compileAuth->invoke($this->compiler, '%auth');
        $this->assertEquals($render, '<?php if (auth()->check()): ?>');

        $render = $compileAuth->invoke($this->compiler, '%auth ');
        $this->assertEquals($render, '<?php if (auth()->check()): ?>');

        $render = $compileAuth->invoke($this->compiler, '%auth("admin")');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?>');

        $render = $compileAuth->invoke($this->compiler, '%auth ("admin")');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?>');

        $render = $compileAuth->invoke($this->compiler, '%auth ("admin") ');
        $this->assertEquals($render, '<?php if (auth("admin")->check()): ?> ');
    }

    public function testCompileGuestStatement()
    {
        $compileGuest = $this->makeReflectionFor('compileGuest');

        $render = $compileGuest->invoke($this->compiler, '%guest');
        $this->assertEquals($render, '<?php if (!auth()->check()): ?>');

        $render = $compileGuest->invoke($this->compiler, '%guest ');
        $this->assertEquals($render, '<?php if (!auth()->check()): ?>');

        $render = $compileGuest->invoke($this->compiler, '%guest("admin")');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?>');

        $render = $compileGuest->invoke($this->compiler, '%guest ("admin")');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?>');

        $render = $compileGuest->invoke($this->compiler, '%guest ("admin") ');
        $this->assertEquals($render, '<?php if (!auth("admin")->check()): ?> ');
    }

    public function testCompileLangStatement()
    {
        $compileLang = $this->makeReflectionFor('compileLang');

        $render = $compileLang->invoke($this->compiler, '%lang("fr")');
        $this->assertEquals($render, '<?php if (client_locale() == "fr"): ?>');

        $render = $compileLang->invoke($this->compiler, '%lang("fr") ');
        $this->assertEquals($render, '<?php if (client_locale() == "fr"): ?> ');
    }

    public function testCompileCsrfStatement()
    {
        $compileCsrf = $this->makeReflectionFor('compileCsrf');

        $render = $compileCsrf->invoke($this->compiler, '%csrf');
        $this->assertEquals($render, '<?= csrf_field(); ?>');

        $render = $compileCsrf->invoke($this->compiler, '%csrf ');
        $this->assertEquals($render, '<?= csrf_field(); ?> ');

        $render = $compileCsrf->invoke($this->compiler, ' %csrf ');
        $this->assertEquals($render, ' <?= csrf_field(); ?> ');

        $render = $compileCsrf->invoke($this->compiler, '%csrf()');
        $this->assertEquals($render, '<?= csrf_field(); ?>()');
    }

    public function testCompileEnvStatement()
    {
        $compileEnv = $this->makeReflectionFor('compileEnv');

        $render = $compileEnv->invoke($this->compiler, '%env("production")');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?>');

        $render = $compileEnv->invoke($this->compiler, '%env("production") ');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?> ');
    }

    public function testCompileProductionStatement()
    {
        $compileProduction = $this->makeReflectionFor('compileProduction');

        $render = $compileProduction->invoke($this->compiler, '%production');
        $this->assertEquals($render, '<?php if (app_mode() == "production"): ?>');

        $render = $compileProduction->invoke($this->compiler, '%production("hello world")');
        $this->assertEquals($render, "<?php throw new \Tintin\Exception\BadDirectiveCalledException('The %production cannot take the parameters!') ?>");
    }

    public function testCompileHasFlashStatement()
    {
        $compileFlash = $this->makeReflectionFor('compileHasFlash');

        $render = $compileFlash->invoke($this->compiler, '%hasflash("error")');
        $this->assertEquals($render, '<?php if (session()->has("error")): ?>');
    }

    public function testCompileFlashStatement()
    {
        $compileFlash = $this->makeReflectionFor('compileFlash');

        $render = $compileFlash->invoke($this->compiler, '%flash("error")');
        $this->assertEquals($render, '<?php echo session()->get("error"); ?>');
    }

    public function testCompileEmptyStatement()
    {
        $compileFlash = $this->makeReflectionFor('compileEmpty');

        $render = $compileFlash->invoke($this->compiler, '%empty($users)');
        $this->assertEquals($render, '<?php if (empty($users)): ?>');
    }

    public function testCompileNotEmptyStatement()
    {
        $compileFlash = $this->makeReflectionFor('compileNotEmpty');

        $render = $compileFlash->invoke($this->compiler, '%notempty($users)');
        $this->assertEquals($render, '<?php if (!empty($users)): ?>');
    }

    public function testcompileEndHelpersStatement()
    {
        $compileHelper = $this->makeReflectionFor('compileEndHelpers');

        $render = $compileHelper->invoke($this->compiler, '%endauth');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endguest');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endlang');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endenv');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endempty');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endnotempty');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endproduction');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileHelper->invoke($this->compiler, '%endhasflash');
        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__ . '/view/auth.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if (auth()->check()): ?>', $render);
        $this->assertStringContainsString('<?php if (!auth()->check()): ?>', $render);
    }
}
