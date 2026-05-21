<?php

use Tintin\Tintin;
use Tintin\Filesystem;

class TintinTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Filesystem
     */
    private Filesystem $loader;

    /**
     * @var Tintin
     */
    private Tintin $instance;

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

    /**
     * Test simple rendering 1
     */
    public function testRenderSimpleData()
    {
        $tintin = new Tintin();

        $render = $tintin->render('{{ $name }}', ['name' => "Tintin"]);

        $this->assertEquals($render, 'Tintin');
    }

    /**
     * Test simple rendering
     *
     * @dataProvider getComputeData
     */
    public function testRenderSimpleDataCompute(int $value, string $sign, int $result)
    {
        $tintin = new Tintin();

        $render = $tintin->render('{{{ $value ' . $sign . ' $value }}}', compact('value'));

        $this->assertEquals($render, $result);
    }

    /**
     * Test false custom directive rendering
     */
    public function testRenderFalseDirective()
    {
        $tintin = new Tintin();

        $render = $tintin->render('%falseDirective <href="%link">');

        $this->assertEquals($render, '%falseDirective <href="%link">');
    }

    /**
     * Blank lines and indentation inside a code snippet must be preserved.
     * Regression: executePlainRendering trim()-ed every line, destroying snippet whitespace.
     */
    public function testRenderPreservesBlankLinesInCodeSnippet()
    {
        $tintin = new Tintin();

        $template = "<pre><code>\n"
            . "function foo() {\n"
            . "\n"
            . "    return 42;\n"
            . "}\n"
            . "</code></pre>";

        $render = $tintin->render($template);

        $this->assertStringContainsString("function foo() {\n\n    return 42;", $render);
    }

    /**
     * The compute dataset
     *
     * @return array
     */
    public function getComputeData()
    {
        return [
            ['value' => 5, 'sign' => '+', 'result' => 10],
            ['value' => 5, 'sign' => '-', 'result' => 0],
            ['value' => 5, 'sign' => '*', 'result' => 25],
            ['value' => 5, 'sign' => '/', 'result' => 1],
        ];
    }
}
