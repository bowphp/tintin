<?php

use Tintin\Compiler;

class CompileJsonTest extends \PHPUnit\Framework\TestCase
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

    public function testCompileJson()
    {
        $compile_json = $this->makeReflectionFor('compileJson');

        $render = $compile_json->invoke($this->compiler, "%json(['name' => 'tintin'])");

        $this->assertEquals($render, "<?php echo json_encode(['name' => 'tintin']); ?>");
    }

    public function testCompileJsonWithEncodingOptions()
    {
        $compile_json = $this->makeReflectionFor('compileJson');

        $render = $compile_json->invoke($this->compiler, "%json(['name' => 'tintin'], JSON_PRETTY_PRINT)");

        $this->assertEquals($render, "<?php echo json_encode(['name' => 'tintin'], JSON_PRETTY_PRINT, 512); ?>");
    }

    public function testCompileJsonWithEncodingOptionsAndDepth()
    {
        $compile_json = $this->makeReflectionFor('compileJson');

        $render = $compile_json->invoke($this->compiler, "%json(['name' => 'tintin'], JSON_PRETTY_PRINT, 12)");

        $this->assertEquals($render, "<?php echo json_encode(['name' => 'tintin'], JSON_PRETTY_PRINT, 12); ?>");
    }
}
