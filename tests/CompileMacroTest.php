<?php

use Tintin\Compiler;

class CompileMacroTest extends PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    private Compiler $compiler;

    public function setUp(): void
    {
        $this->compiler = new Compiler();
    }

    public function testCompileMacroExtraction()
    {
        $template = file_get_contents(__DIR__ . '/view/macro.tintin.php');

        $compileMacro = $this->makeReflectionFor('compileMacroExtraction');
        $output = $compileMacro->invoke($this->compiler, $template);

        $this->assertStringContainsString("", $output);
    }

    public function testCompileImport()
    {
        $compileMacro = $this->makeReflectionFor('compileImport');
        $output = $compileMacro->invoke($this->compiler, "%import('helpers')");

        $this->assertStringContainsString("<?php echo \$__tintin->getMacroManager()->make(\"helpers\", ['__tintin' => \$__tintin]); ?>", $output);
    }
}
