<?php

use Spatie\Snapshots\MatchesSnapshots;
use Tintin\Tintin;
use Tintin\Filesystem;
use Tintin\LoaderInterface;

class CompileCustomDirectiveTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;
    use MatchesSnapshots;

    /**
     * @var Tintin
     */
    private Tintin $tintin;

    /**
     * @var ?LoaderInterface;
     */
    private ?LoaderInterface $loader = null;

    /**
     * On setup
     */
    public function setUp(): void
    {
        $this->tintin = new Tintin();

        $this->loader = new Filesystem([
            'path' => __DIR__ . '/view',
            'extension' => 'tintin.php',
            'cache' => __DIR__ . '/cache'
        ]);
    }

    public function testHelloDirective()
    {
        $this->tintin->directive('hello', function ($title, $name) {
            return "Hello $title $name";
        });

        $render = $this->tintin->render('%hello("Tintin", "Bow")');

        $this->assertEquals($render, "Hello Tintin Bow");
    }

    public function testSimpleDirective()
    {
        $now = time();
        $this->tintin->directive('now', function () use ($now) {
            return $now;
        });

        $r = $this->tintin->render('%now');

        $this->assertEquals($now, $r);
    }

    public function testComplexDirective()
    {
        $this->tintin->directive('input', function (array $attribute) {
            return '<input type="' . $attribute['type'] . '" name="' . $attribute['name'] . '" value="' . $attribute['value'] . '" />';
        });

        $render = $this->tintin->render('%input(["type" => "text", "value" => null, "name" => "name"])');

        $this->assertEquals($render, '<input type="text" name="name" value="" />');
    }

    public function testCompileCustomDirectiveWithPlanParameters()
    {
        $tintin = new Tintin();

        $tintin->directive('greeting', function (string $name) {
            return "Hello $name";
        });

        $render = $tintin->render('%greeting($name)', ['name' => 'franck']);

        $this->assertEquals(trim($render), 'Hello franck');
    }

    public function testCompileCustomDirectiveWithBrakeLineParameters()
    {
        $tintin = new Tintin();
        $compiler = $tintin->getCompiler();

        $tintin->directive('user', function (array $info) {
            return "Hello {$info['name']}, {$info['lastname']}";
        });

        $template = <<<TEMPLATE
%user([
    "name" => \$name,
    "lastname" => \$lastname
])
TEMPLATE;

        $compileCustomDirective = $this->makeReflectionFor('compileCustomDirective');
        $output = $compileCustomDirective->invoke($compiler, $template);
        $this->assertEquals($output, "@__tintin_custom__user__directive__@");

        $output = $tintin->renderString($template, ["name" => "bow", "lastname" => "tintin"]);
        $this->assertEquals("Hello bow, tintin", $output);

        $output = $compileCustomDirective->invoke($compiler, '%user([
            "name" => $name,
            "lastname" => $lastname
        ])');
        $this->assertEquals($output, "@__tintin_custom__user__directive__@");

        $output = $tintin->renderString('%user([
            "name" => $name,
            "lastname" => $lastname
        ])', ["name" => "bow", "lastname" => "tintin"]);
        $this->assertEquals("Hello bow, tintin", $output);
    }

    public function testCompileCustomDirectiveDefineAsBrockenClause()
    {
        $tintin = new Tintin($this->loader);

        $tintin->directive('greeting', function (string $name) {
            return "Hello, $name";
        });

        $tintin->directive('admin', function () {
            return '<?php if (true): ?>';
        }, true);

        $tintin->directive('endadmin', function () {
            return '<?php endif; ?>';
        }, true);

        $output = $tintin->render('custom', ['name' => 'franck']);

        $this->assertStringContainsString('Franck, access allowed', trim($output));
        $this->assertStringContainsString('Hello, franck', trim($output));
        $this->assertMatchesTextSnapshot($output);
    }
}
