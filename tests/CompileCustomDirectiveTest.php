<?php

use Tintin\Tintin;
use Tintin\Loader\Filesystem;
use Tintin\Loader\LoaderInterface;

class CompileCustomDirectiveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tintin
     */
    private Tintin $tintin;

    /**
     * @var LoaderInterface;
     */
    private LoaderInterface $loader;

    /**
     * On setup
     */
    public function setUp(): void
    {
        $this->tintin = new Tintin;

        $this->loader = new Filesystem([
            'path' => __DIR__.'/view',
            'extension' => 'tintin.php',
            'cache' => __DIR__.'/cache'
        ]);
    }

    /**
     * @throws \Tintin\Exception\DirectiveNotAllowException
     */
    public function testHelloDirective()
    {
        $this->tintin->directive('hello', function (array $attributes = []) {
            return 'Hello ' . implode(" ", $attributes);
        });

        $r = $this->tintin->render('#hello("Tintin", "Bow")');

        $this->assertEquals($r, "Hello Tintin Bow");
    }

    /**
     * @throws \Tintin\Exception\DirectiveNotAllowException
     */
    public function testSimpleDirective()
    {
        $now = time();
        $this->tintin->directive('now', function (array $attributes = []) use ($now) {
            return $now;
        });

        $r = $this->tintin->render('#now');

        $this->assertEquals($now, $r);
    }

    /**
     * @throws \Tintin\Exception\DirectiveNotAllowException
     */
    public function testComplexDirective()
    {
        $this->tintin->directive('input', function (array $attributes = []) {
            $attribute = $attributes[0];

            return '<input type="'.$attribute['type'].'" name="'.$attribute['name'].'" value="'.$attribute['value'].'" />';
        });

        $r = $this->tintin->render('#input(["type" => "text", "value" => null, "name" => "name"])');

        $this->assertEquals($r, '<input type="text" name="name" value="" />');
    }

    /**
     * @throws \Tintin\Exception\DirectiveNotAllowException
     */
    public function testCompileCustomDirectiveDefineAsBrockenClause()
    {
        $tintin = new Tintin($this->loader);

        $tintin->directive('admin', function (array $expression) {
            return '<?php if (true): ?>';
        }, true);

        $tintin->directive('endadmin', function (array $expression) {
            return '<?php endif; ?>';
        }, true);

        $render = $tintin->render('custom', ['name' => 'Tintin access allowed']);

        $this->assertEquals(trim($render), 'Tintin access allowed');
    }
}
