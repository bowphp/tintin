<?php

use Tintin\Tintin;
use Tintin\Compiler;
use Tintin\Loader\Filesystem;

class CompileVerbatimTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    private Compiler $compiler;

    public function setUp(): void
    {
        $this->compiler = new Compiler();
    }
 
    public function testCompileBasiclyTheVerbatimTag()
    {
        $template = "%verbatim\n\t%if (true)\n\t\t{{ \$name }}\n\t%endif\n%endverbatim";
        $compileVerbatim = $this->makeReflectionFor('compileVerbatim');

        $output = $compileVerbatim->invoke($this->compiler, $template);
        $this->assertEquals("@__tintin_verbatim__1__@", $output);

        $output = $compileVerbatim->invoke($this->compiler, $template);
        $this->assertEquals("@__tintin_verbatim__2__@", $output);

        $output = $compileVerbatim->invoke($this->compiler, $template);
        $this->assertEquals("@__tintin_verbatim__3__@", $output);
    }

    public function testCompileBasiclyTheVerbatimTagShouldFail()
    {
        $template = "%verbatim\n\t%if (true)\n\t\t{{ \$name }}\n\t%endif\n";
        $compileVerbatim = $this->makeReflectionFor('compileVerbatim');
        $output = $compileVerbatim->invoke($this->compiler, $template);

        $this->assertNotEquals("@__tintin_verbatim__1__@", $output);
        $this->assertEquals($template, $output);

        $ref = new ReflectionProperty($this->compiler, "verbatim_accumulator");
        $ref->setAccessible(true);

        $this->assertEquals(0, count($ref->getValue($this->compiler)));
    }

    public function testCompileVerbatimTagAsRawFromFile()
    {
        $compileVerbatim = $this->makeReflectionFor('compileVerbatim');
        $output = $compileVerbatim->invoke($this->compiler, file_get_contents(
            __DIR__ . '/view/verbatim.tintin.php'
        ));

        $this->assertStringContainsString("@__tintin_verbatim__1__@", $output);
        $this->assertStringContainsString("@__tintin_verbatim__2__@", $output);
        $this->assertStringContainsString("@__tintin_verbatim__3__@", $output);

        $this->assertStringNotContainsString("%guest\nA guest session\n%endguest", $output);
        $this->assertStringNotContainsString("%if (true)\n{{{ \$name }}}\n%endif", $output);
        $this->assertStringNotContainsString("%auth\nA auth session\n%endif", $output);
    }

    public function testCompileVerbatimTagAsRawFromFileAfterParsing()
    {
        $compiler = new Compiler();
        $output = $compiler->compile(file_get_contents(
            __DIR__ . '/view/verbatim.tintin.php'
        ));

        $this->assertStringContainsString("%guest", $output);
        $this->assertStringContainsString("%if (true)", $output);
        $this->assertStringContainsString("%auth", $output);

        $this->assertStringNotContainsString("@__tintin_verbatim__1__@", $output);
        $this->assertStringNotContainsString("@__tintin_verbatim__2__@", $output);
        $this->assertStringNotContainsString("@__tintin_verbatim__3__@", $output);
    }
}
