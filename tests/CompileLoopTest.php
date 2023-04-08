<?php

use Tintin\Compiler;

class CompileLoopTest extends \PHPUnit\Framework\TestCase
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

    /**
     * Test %While statement
     */
    public function testCompileWhile()
    {
        $compile_while = $this->makeReflectionFor('compileWhile');

        $render = $compile_while->invoke(new Compiler, '%while ($name != "Tintin")');

        $this->assertEquals($render, '<?php while ($name != "Tintin"): ?>');

        $compile_endwhile = $this->makeReflectionFor('compileEndWhile');

        $render = $compile_endwhile->invoke(new Compiler, '%endwhile');

        $this->assertEquals($render, '<?php endwhile; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileForeach()
    {
        $compile_foreach = $this->makeReflectionFor('compileForeach');

        $render = $compile_foreach->invoke(new Compiler, '%loop ($names as $name)');

        $this->assertEquals($render, '<?php foreach ($names as $name): ?>');

        $compile_endforeach = $this->makeReflectionFor('compileEndForeach');

        $render = $compile_endforeach->invoke(new Compiler, '%endloop');

        $this->assertEquals($render, '<?php endforeach; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileFor()
    {
        $compile_for = $this->makeReflectionFor('compileFor');

        $render = $compile_for->invoke(new Compiler, '%for ($i = 0; $i < 10; $i++)');

        $this->assertEquals($render, '<?php for ($i = 0; $i < 10; $i++): ?>');

        $compile_endfor = $this->makeReflectionFor('compileEndFor');

        $render = $compile_endfor->invoke(new Compiler, '%endfor');

        $this->assertEquals($render, '<?php endfor; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileBreaker()
    {
        $compile_continue = $this->makeReflectionFor('compileContinue');

        $render = $compile_continue->invoke(new Compiler, '%jump');

        $this->assertEquals($render, '<?php continue; ?>');

        $render = $compile_continue->invoke(new Compiler, '%jump ($name == "Tintin")');

        $this->assertEquals($render, '<?php if ($name == "Tintin"): continue; endif; ?>');
    }
}
