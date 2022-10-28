<?php

use Tintin\Tintin;
use Tintin\Compiler;
use Tintin\Loader\Filesystem;

class CompileExtendsTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    /**
     * @var Filesystem
     */
    private Filesystem $loader;

    /**
     * On setup
     */
    public function setUp(): void
    {
        $this->loader = new Filesystem([
          'path' => __DIR__.'/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__.'/cache'
        ]);
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertInstanceOf(Filesystem::class, $this->loader);
        
        $instance = new Tintin($this->loader);

        $this->assertInstanceOf(Tintin::class, $instance);
    }

    public function testCompileExtendsStatement()
    {
        $compiler = new Compiler;
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "#extends('layout')");

        $class = $compileExtends->getDeclaringClass();
        $extends_render = $class->getProperty('extends_render');
        $extends_render->setAccessible(true);
        $value = $extends_render->getValue($compiler);
        $render = end($value);

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('layout', ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileExtendsStatementWithParam()
    {
        $compiler = new Compiler;
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "#extends('layout', ['name' => 'Bow'])");

        $class = $compileExtends->getDeclaringClass();
        $extends_render = $class->getProperty('extends_render');
        $extends_render->setAccessible(true);
        $value = $extends_render->getValue($compiler);
        $render = end($value);

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('layout', ['name' => 'Bow'], ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileExtendsStatementWithParamComplex()
    {
        $compiler = new Compiler;
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "#extends('layout', ['name' => 'Bow', 'is_admin' => isset(\$is_admin)])");

        $class = $compileExtends->getDeclaringClass();
        $extends_render = $class->getProperty('extends_render');
        $extends_render->setAccessible(true);
        $value = $extends_render->getValue($compiler);
        $render = end($value);

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('layout', ['name' => 'Bow', 'is_admin' => isset(\$is_admin)], ['__tintin' => \$__tintin]); ?>");
    }

    public function testShouldStackInstance()
    {
        $tintin = new Tintin($this->loader);

        $stack_manager = $tintin->getStackManager();

        $this->assertInstanceOf(\Tintin\Stacker\StackManager::class, $stack_manager);
    }

    public function testShouldRendStack()
    {
        $tintin = new Tintin($this->loader);

        $stack_manager = $tintin->getStackManager();

        $stack_manager->startStack('name');
        echo 'Tintin';
        $stack_manager->endStack();

        $stack_manager->startStack('hello');
        echo 'Hello';
        $stack_manager->endStack();

        $stack_manager->startStack('pack', 'Tintin template');

        $this->assertEquals('Hello', $stack_manager->getStack('hello'));
        $this->assertEquals('Tintin', $stack_manager->getStack('name'));
        $this->assertEquals('Tintin template', $stack_manager->getStack('pack'));
    }
}
