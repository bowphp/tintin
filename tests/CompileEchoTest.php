<?php

use Tintin\Compiler;

class CompilerEchoTest extends \PHPUnit\Framework\TestCase
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
     * Test echo
     *
     * @throws ReflectionException
     */
    public function testCompileEcho()
    {
        $compileEcho = $this->makeReflectionFor('compileEcho');
        
        $render = $compileEcho->invoke(new Compiler, '{{ "hello world" }}');

        $this->assertEquals($render, '<?php echo htmlspecialchars("hello world", ENT_QUOTES); ?>');
    }

    /**
     * Test row echo
     *
     * @throws ReflectionException
     */
    public function testCompileRawEcho()
    {
        $compileRawEcho = $this->makeReflectionFor('compileRawEcho');
        
        $render = $compileRawEcho->invoke(new Compiler, '{{{ "hello world" }}}');

        $this->assertEquals($render, '<?php echo "hello world"; ?>');
    }
}
