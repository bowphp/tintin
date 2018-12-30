<?php

use Tintin\Tintin;
use Tintin\Loader\Filesystem;

class CompileExtendsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var
     */
    private $loader;

    /**
     * On setup
     */
    public function setUp()
    {
        $this->loader = new Filesystem([
          'path' => __DIR__.'/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__.'/cache'
        ]);
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertInstanceOf(\Tintin\Loader\Filesystem::class, $this->loader);
        
        $instance = new Tintin($this->loader);

        $this->assertInstanceOf(Tintin::class, $instance);
    }

    public function testStack()
    {
        $tintin = new Tintin($this->loader);

        $stack_manager = $tintin->getStackManager();

        $this->assertInstanceOf(\Tintin\Stacker\StackManager::class, $stack_manager);

        $stack_manager->startStack('name');
        echo 'Tintin';
        $stack_manager->endStack();

        $this->assertEquals('Tintin', $stack_manager->getStack('name'));

        $stack_manager->startStack('hello');
        echo 'Hello';
        $stack_manager->endStack();

        $this->assertEquals('Hello', $stack_manager->getStack('hello'));

        $stack_manager->startStack('pack', 'Tintin template');

        $this->assertEquals('Tintin template', $stack_manager->getStack('pack'));
    }
}
