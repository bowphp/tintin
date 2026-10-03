<?php

use Tintin\Tintin;
use Tintin\Compiler;
use Tintin\Filesystem;

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
          'path' => __DIR__ . '/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__ . '/cache'
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
        $compiler = new Compiler();
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "%extends('layout')");

        $class = $compileExtends->getDeclaringClass();
        $extends_render = $class->getProperty('extends_render');
        $extends_render->setAccessible(true);
        $value = $extends_render->getValue($compiler);
        $render = end($value);

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('layout', ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileExtendsStatementWithParam()
    {
        $compiler = new Compiler();
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "%extends('layout', ['name' => 'Bow'])");

        $class = $compileExtends->getDeclaringClass();
        $extends_render = $class->getProperty('extends_render');
        $extends_render->setAccessible(true);
        $value = $extends_render->getValue($compiler);
        $render = end($value);

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('layout', ['name' => 'Bow'], ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileExtendsStatementWithParamComplex()
    {
        $compiler = new Compiler();
        $compileExtends = $this->makeReflectionFor('compileExtends');
        $render = $compileExtends->invoke($compiler, "%extends('layout', ['name' => 'Bow', 'is_admin' => isset(\$is_admin)])");

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

        $this->assertInstanceOf(\Tintin\StackManager::class, $stack_manager);
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

    /**
     * A block body is compiled and executed once, together with the page.
     * Its captured output must be returned as-is and never compiled a second
     * time, otherwise template syntax that reached the block through data
     * (e() escapes < > & " ' but not braces) would be executed: RCE.
     */
    public function testStackContentIsNotReEvaluated()
    {
        $tintin = new Tintin($this->loader);

        $stack_manager = $tintin->getStackManager();

        // This is exactly what the output buffer holds after the first render
        // when a value like {{ 7*7 }} arrives through the block's data.
        $stack_manager->startStack('content');
        echo 'Hello {{ 7*7 }}';
        $stack_manager->endStack();

        $output = $stack_manager->getStack('content');

        $this->assertStringContainsString('{{ 7*7 }}', $output);
        $this->assertStringNotContainsString('49', $output);
    }

    /**
     * End-to-end: a value carrying template syntax rendered inside a %block
     * and exposed through %inject must come back as literal text.
     */
    public function testBlockDataIsNotExecutedThroughLayout()
    {
        $tintin = new Tintin($this->loader);

        $output = $tintin->render('security', ['name' => '{{ 7*7 }}']);

        $this->assertStringContainsString('{{ 7*7 }}', $output);
        $this->assertStringNotContainsString('49', $output);
    }

    /**
     * User data must not be able to overwrite the engine's own $__tintin
     * handle through extract(); otherwise every block/inject page crashes
     * (DoS) or the handle can be spoofed.
     */
    public function testDataCannotOverwriteEngineHandle()
    {
        $tintin = new Tintin($this->loader);

        $output = $tintin->render('security', [
            'name' => 'Alice',
            '__tintin' => 'pwned',
        ]);

        $this->assertStringContainsString('Hello Alice', $output);
    }

    /**
     * User data must not be able to redirect which template file is required
     * by overwriting $__template through extract() (view-selection hijack).
     */
    public function testDataCannotHijackTemplateSelection()
    {
        $tintin = new Tintin($this->loader);

        $output = $tintin->render('security', [
            'name' => 'Alice',
            '__template' => 'layout',
            '__path' => '/etc/passwd',
        ]);

        // The 'security' view is rendered, not 'layout' or an injected path.
        $this->assertStringContainsString('Hello Alice', $output);
        $this->assertStringNotContainsString('root:', $output);
    }
}
