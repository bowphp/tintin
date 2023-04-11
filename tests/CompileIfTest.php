<?php

use Tintin\Compiler;

class CompileIfTest extends \PHPUnit\Framework\TestCase
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

    public function testCompileIfStatement()
    {
        $compile_if = $this->makeReflectionFor('compileIf');

        $render = $compile_if->invoke($this->compiler, '%if ($name > 0)');

        $this->assertEquals($render, '<?php if ($name > 0): ?>');

        $render = $compile_if->invoke($this->compiler, '%if($name > 0)');

        $this->assertEquals($render, '<?php if ($name > 0): ?>');
    }

    public function testCompileElseStatement()
    {
        $compile_else = $this->makeReflectionFor('compileElse');

        $render = $compile_else->invoke($this->compiler, '%else');

        $this->assertEquals($render, '<?php else: ?>');
    }

    public function testCompileElseIfStatement()
    {
        $compile_else = $this->makeReflectionFor('compileElseIf');

        $render = $compile_else->invoke($this->compiler, '%elseif ($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');

        $render = $compile_else->invoke($this->compiler, '%elseif($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');

        $compile_elif = $this->makeReflectionFor('compileElseIfAlias');
        $render = $compile_elif->invoke($this->compiler, '%elif ($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');
    }

    public function testCompileUnLessStatement()
    {
        $compile_else = $this->makeReflectionFor('compileUnLess');

        $render = $compile_else->invoke($this->compiler, '%unless ($name > 0)');
        $this->assertEquals($render, '<?php if (! ($name > 0)): ?>');

        $render = $compile_else->invoke($this->compiler, '%unless($name > 0)');
        $this->assertEquals($render, '<?php if (! ($name > 0)): ?>');
    }

    public function testCompileIssetStatement()
    {
        $compile_else = $this->makeReflectionFor('compileIsset');

        $render = $compile_else->invoke($this->compiler, '%isset ($name)');
        $this->assertEquals($render, '<?php if (isset($name)): ?>');

        $render = $compile_else->invoke($this->compiler, '%isset($name)');
        $this->assertEquals($render, '<?php if (isset($name)): ?>');
    }

    public function testcompileEndifStatement()
    {
        $compile_else = $this->makeReflectionFor('compileEndIf');

        $render = $compile_else->invoke($this->compiler, '%endif');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endunless');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke($this->compiler, '%endisset');
        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__ . '/view/sample.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if ($age > 16): ?>', $render);
    }
}
