<?php

use Tintin\Tintin;
use Tintin\Compiler;
use Tintin\Filesystem;
use Spatie\Snapshots\MatchesSnapshots;

class CompileMacroTest extends PHPUnit\Framework\TestCase
{
    use CompileClassReflection;
    use MatchesSnapshots;

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

        $property = new ReflectionProperty($this->compiler, "macros");
        $property->setAccessible(true);
        $macros = $property->getValue($this->compiler);

        $this->assertEquals("", $output);
        $this->assertNotEmpty($macros);
        $this->assertArrayHasKey("greeting", $macros);
        $this->assertArrayHasKey("sum", $macros);
    }

    public function testCompileImport()
    {
        $compileMacro = $this->makeReflectionFor('compileImport');
        $output = $compileMacro->invoke($this->compiler, "%import('helpers')");

        $property = new ReflectionProperty($this->compiler, "imports_render");
        $property->setAccessible(true);
        $imports_render = $property->getValue($this->compiler);

        $this->assertEquals("", $output);
        $this->assertNotEmpty($imports_render);
        $this->assertStringContainsString(
            "<?php \$__tintin->getMacroManager()->make(\"helpers\"); ?>",
            $imports_render[0]
        );
    }

    public function testCompileFullImportTemplate()
    {
        $loader = new Filesystem([
            'path' => __DIR__ . '/view',
            'extension' => 'tintin.php',
            'cache' => __DIR__ . '/cache'
        ]);

        $tintin = new Tintin($loader);
        $output = $tintin->render("macro-template");

        $this->assertStringContainsString("Hello, Papac", $output);
        $this->assertStringContainsString("User's Franck", $output);
        $this->assertStringContainsString("User's Lucien", $output);
        $this->assertStringContainsString("User's Brice", $output);
        $this->assertStringContainsString("Sum of 1 + 2 = 3", $output);
        $this->assertStringContainsString('<input type="text" name="name" value="papac"/>', $output);
        $this->assertMatchesTextSnapshot(trim($output));
    }
}
