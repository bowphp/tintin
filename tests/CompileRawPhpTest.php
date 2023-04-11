<?php

use Tintin\Compiler;

class CompileRawPhpTest extends \PHPUnit\Framework\TestCase
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

    public function testCompileRawPhp()
    {
        $compile_raw_php = $this->makeReflectionFor('compileRawPhp');

        $render = $compile_raw_php->invoke(new Compiler(), '%raw echo "Hello, world"; %endraw');

        $this->assertEquals($render, '<?php echo "Hello, world"; ?>');
    }
}
