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
        $this->compiler = new Compiler;
    }

    public function testCompileIfStatement()
    {
        $compile_if = $this->makeReflectionFor('compileIf');
        
        $render = $compile_if->invoke(new Compiler, '%if ($name > 0)');

        $this->assertEquals($render, '<?php if ($name > 0): ?>');

        $render = $compile_if->invoke(new Compiler, '%if($name > 0)');

        $this->assertEquals($render, '<?php if ($name > 0): ?>');
    }

    public function testCompileElseStatement()
    {
        $compile_else = $this->makeReflectionFor('compileElse');
        
        $render = $compile_else->invoke(new Compiler, '%else');

        $this->assertEquals($render, '<?php else: ?>');
    }

    public function testCompileElseIfStatement()
    {
        $compile_else = $this->makeReflectionFor('compileElseIf');
        
        $render = $compile_else->invoke(new Compiler, '%elseif ($name > 0)');

        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');
        
        $render = $compile_else->invoke(new Compiler, '%elseif($name > 0)');

        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');
    }

    public function testCompileUnLessStatement()
    {
        $compile_else = $this->makeReflectionFor('compileUnLess');
        
        $render = $compile_else->invoke(new Compiler, '%unless ($name > 0)');

        $this->assertEquals($render, '<?php if (! ($name > 0)): ?>');

        $render = $compile_else->invoke(new Compiler, '%unless($name > 0)');

        $this->assertEquals($render, '<?php if (! ($name > 0)): ?>');
    }

    public function testcompileEndifStatement()
    {
        $compile_else = $this->makeReflectionFor('compileEndIf');
        
        $render = $compile_else->invoke(new Compiler, '%endif');

        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compile_else->invoke(new Compiler, '%endunless');

        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__.'/view/sample.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if ($age > 16): ?>', $render);
    }
}
