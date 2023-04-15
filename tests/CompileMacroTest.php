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

    public function testCompileMacro()
    {
        $template = file_get_contents(__DIR__.'/view/macro.tintin.php');

        $compileMacro = $this->makeReflectionFor('compileMacro');
        $output = $compileMacro->invoke($this->compiler, $template);

        $this->assertStringContainsString("Hello {{ \$name }}", $output);
    }

    public function testCompileImport()
    {
        $compileMacro = $this->makeReflectionFor('compileImport');
        $output = $compileMacro->invoke($this->compiler, "%import('helpers')");

        $this->assertStringContainsString("<?php echo \$__tintin->getMacroManager()->make(\"helpers\", ['__tintin' => \$__tintin]); ?>", $output);
    }
}
