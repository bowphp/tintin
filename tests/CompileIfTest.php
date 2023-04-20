<?php

use Spatie\Snapshots\MatchesSnapshots;
use Tintin\Compiler;

class CompileIfTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;
    use MatchesSnapshots;

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
        $compileIf = $this->makeReflectionFor('compileIf');

        $render = $compileIf->invoke($this->compiler, '%if ($name > 0)');
        $this->assertEquals($render, '<?php if ($name > 0): ?>');

        $render = $compileIf->invoke($this->compiler, '%if($name > 0)');
        $this->assertEquals($render, '<?php if ($name > 0): ?>');

        $render = $compileIf->invoke($this->compiler, '%if(isset($name) && $name == "papac")');
        $this->assertEquals($render, '<?php if (isset($name) && $name == "papac"): ?>');
    }

    public function testCompileElseStatement()
    {
        $compileElse = $this->makeReflectionFor('compileElse');

        $render = $compileElse->invoke($this->compiler, '%else');

        $this->assertEquals($render, '<?php else: ?>');
    }

    public function testCompileElseIfStatement()
    {
        $compileElse = $this->makeReflectionFor('compileElseIf');

        $render = $compileElse->invoke($this->compiler, '%elseif ($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');

        $render = $compileElse->invoke($this->compiler, '%elseif($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');

        $render = $compileElse->invoke($this->compiler, '%elseif(isset($name) && $name == "papac")');
        $this->assertEquals($render, '<?php elseif (isset($name) && $name == "papac"): ?>');

        $compileelIf = $this->makeReflectionFor('compileElseIfAlias');
        $render = $compileelIf->invoke($this->compiler, '%elif ($name > 0)');
        $this->assertEquals($render, '<?php elseif ($name > 0): ?>');

        $render = $compileelIf->invoke($this->compiler, '%elif (isset($name) && $name == "papac")');
        $this->assertEquals($render, '<?php elseif (isset($name) && $name == "papac"): ?>');
    }

    public function testCompileUnLessStatement()
    {
        $compileUnless = $this->makeReflectionFor('compileUnLess');

        $render = $compileUnless->invoke($this->compiler, '%unless ($name > 0)');
        $this->assertEquals($render, '<?php if (! ($name > 0)): ?>');

        $render = $compileUnless->invoke($this->compiler, '%unless(isset($name) && $name == "papac")');
        $this->assertEquals($render, '<?php if (! (isset($name) && $name == "papac")): ?>');
    }

    public function testCompileIssetStatement()
    {
        $compileIsset = $this->makeReflectionFor('compileIsset');

        $render = $compileIsset->invoke($this->compiler, '%isset ($name)');
        $this->assertEquals($render, '<?php if (isset($name)): ?>');

        $render = $compileIsset->invoke($this->compiler, '%isset($name)');
        $this->assertEquals($render, '<?php if (isset($name)): ?>');
    }

    public function testcompileEndifStatement()
    {
        $compileEndIf = $this->makeReflectionFor('compileEndIf');

        $render = $compileEndIf->invoke($this->compiler, '%endif');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileEndIf->invoke($this->compiler, '%endunless');
        $this->assertEquals($render, '<?php endif; ?>');

        $render = $compileEndIf->invoke($this->compiler, '%endisset');
        $this->assertEquals($render, '<?php endif; ?>');
    }

    public function testBlockStatement()
    {
        $html = file_get_contents(__DIR__ . '/view/sample.tintin.php');

        $render = $this->compiler->compile($html);

        $this->assertStringContainsString('<?php if ($age > 16): ?>', $render);
        $this->assertMatchesTextSnapshot($render);
    }
}
