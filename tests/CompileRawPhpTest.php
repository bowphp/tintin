<?php

use Tintin\Compiler;

class CompileRawPhpTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Compiler
     */
    private $compiler;

    public function setUp()
    {
        $this->compiler = new Compiler;
    }

    /**
     * Reflection maker
     *
     * @param string $method
     */
    public function makeReflectionFor($method)
    {
        $reflection = new \ReflectionMethod('\Tintin\Compiler', $method);
    
        $reflection->setAccessible(true);

        return $reflection;
    }

    public function testCompileRawPhp()
    {
        $compile_raw_php = $this->makeReflectionFor('compileRawPhp');

        $render = $compile_raw_php->invoke(new Compiler, '#raw echo "Hello, world"; #endraw');

        $this->assertEquals($render, '<?php echo "Hello, world"; ?>');     
    }
}
