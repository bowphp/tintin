<?php

use Tintin\Compiler;

class CompileLoopTest extends \PHPUnit\Framework\TestCase
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

    /**
     * Test %While statement
     */
    public function testCompileWhile()
    {
        $compile_while = $this->makeReflectionFor('compileWhile');

        $render = $compile_while->invoke($this->compiler, '%while ($name != "Tintin")');

        $this->assertEquals($render, '<?php while ($name != "Tintin"): ?>');

        $compile_endwhile = $this->makeReflectionFor('compileEndWhile');

        $render = $compile_endwhile->invoke($this->compiler, '%endwhile');

        $this->assertEquals($render, '<?php endwhile; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileForeach()
    {
        $compile_foreach = $this->makeReflectionFor('compileForeach');

        $render = $compile_foreach->invoke($this->compiler, '%loop ($names as $name)');

        $this->assertEquals($render, '<?php foreach ($names as $name): ?>');

        $compile_endforeach = $this->makeReflectionFor('compileEndForeach');

        $render = $compile_endforeach->invoke($this->compiler, '%endloop');

        $this->assertEquals($render, '<?php endforeach; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileFor()
    {
        $compile_for = $this->makeReflectionFor('compileFor');

        $render = $compile_for->invoke($this->compiler, '%for ($i = 0; $i < 10; $i++)');

        $this->assertEquals($render, '<?php for ($i = 0; $i < 10; $i++): ?>');

        $compile_endfor = $this->makeReflectionFor('compileEndFor');

        $render = $compile_endfor->invoke($this->compiler, '%endfor');

        $this->assertEquals($render, '<?php endfor; ?>');
    }

    /**
     * Test %loop statement
     */
    public function testCompileBreaker()
    {
        $compile_continue = $this->makeReflectionFor('compileContinue');

        $render = $compile_continue->invoke($this->compiler, '%jump');

        $this->assertEquals($render, '<?php continue; ?>');

        $render = $compile_continue->invoke($this->compiler, '%jump ($name == "Tintin")');

        $this->assertEquals($render, '<?php if ($name == "Tintin"): continue; endif; ?>');
    }

    /**
     * Regression: a single-line %loop whose body contains an echo must not
     * have its head extended past the real `)` by the greedy condition
     * pattern (shared with %if).
     */
    public function testInlineLoopWithEchoBody()
    {
        $source = '%loop ($items as $item) <span>{{ $item }}</span> %endloop';

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString('<?php foreach ($items as $item): ?>', $render);
        $this->assertStringContainsString('<?php echo e($item); ?>', $render);
        $this->assertStringContainsString('<?php endforeach; ?>', $render);
        $this->assertStringNotContainsString('): ?>; ?>', $render);
    }

    /**
     * Regression: same greedy-match bug, %while variant.
     */
    public function testInlineWhileWithEchoBody()
    {
        $source = '%while ($i < count($items)) <span>{{ $i }}</span> %endwhile';

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString('<?php while ($i < count($items)): ?>', $render);
        $this->assertStringContainsString('<?php echo e($i); ?>', $render);
        $this->assertStringContainsString('<?php endwhile; ?>', $render);
        $this->assertStringNotContainsString('): ?>; ?>', $render);
    }

    /**
     * Regression: %for has the extra wrinkle of semicolons inside the head.
     * The balanced-paren matcher must still stop at the head's real `)`.
     */
    public function testInlineForWithEchoBody()
    {
        $source = '%for ($i = 0; $i < count($items); $i++) <span>{{ $i }}</span> %endfor';

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString('<?php for ($i = 0; $i < count($items); $i++): ?>', $render);
        $this->assertStringContainsString('<?php echo e($i); ?>', $render);
        $this->assertStringContainsString('<?php endfor; ?>', $render);
        $this->assertStringNotContainsString('): ?>; ?>', $render);
    }

    /**
     * A multi-line %loop expression must compile the same as the single-line form.
     */
    public function testCompileMultilineLoop()
    {
        $template = "%loop(\n    \$arrayes as \$arr\n)\n{{ \$arr }}\n%endloop";

        $output = $this->compiler->compile($template);

        $this->assertStringContainsString('<?php foreach', $output);
        $this->assertStringContainsString('$arrayes as $arr', $output);
        $this->assertStringContainsString('<?php endforeach;', $output);
    }

    /**
     * A bare %empty compiled on its own closes the foreach and opens the empty branch.
     */
    public function testCompileLoopEmpty()
    {
        $compile_loop_empty = $this->makeReflectionFor('compileLoopEmpty');

        $render = $compile_loop_empty->invoke($this->compiler, '%empty');

        $this->assertEquals('<?php endforeach; if ($__tintin_loop_1): ?>', $render);
    }

    /**
     * A %loop with a bare %empty branch renders the fallback when the iterable is empty.
     */
    public function testCompileLoopWithLoopEmpty()
    {
        $source = "%loop(\$users as \$user)\nHello {{ \$user }}\n%empty\nNobody\n%endloop";

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString(
            '<?php $__tintin_loop_1 = true; foreach ($users as $user): $__tintin_loop_1 = false; ?>',
            $render
        );
        $this->assertStringContainsString('<?php endforeach; if ($__tintin_loop_1): ?>', $render);
        $this->assertStringContainsString("Nobody\n<?php endif; ?>", $render);
        $this->assertStringNotContainsString('<?php endforeach; ?>', $render);

        $this->assertSame('Hello aHello b', $this->evaluate($render, ['users' => ['a', 'b']]));
        $this->assertSame('Nobody', $this->evaluate($render, ['users' => []]));
    }

    /**
     * A %loop without a bare %empty keeps compiling to a plain foreach.
     */
    public function testCompileLoopWithoutLoopEmptyIsUnchanged()
    {
        $source = "%loop(\$users as \$user)\nHello {{ \$user }}\n%endloop";

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString('<?php foreach ($users as $user): ?>', $render);
        $this->assertStringContainsString('<?php endforeach; ?>', $render);
        $this->assertStringNotContainsString('__tintin_loop', $render);
    }

    /**
     * Nested loops each get their own empty flag, and a sibling loop after a
     * %empty block is not affected by it.
     */
    public function testCompileNestedLoopEmpty()
    {
        $source = implode("\n", [
            '%loop($groups as $group)',
            '[{{ $group["name"] }}',
            '%loop($group["members"] as $member)',
            '{{ $member }}',
            '%empty',
            'empty-group',
            '%endloop',
            ']',
            '%empty',
            'no-groups',
            '%endloop',
            '%loop($others as $other)',
            '{{ $other }}',
            '%endloop',
        ]);

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString(
            '$__tintin_loop_1 = true; foreach ($groups as $group): $__tintin_loop_1 = false;',
            $render
        );
        $this->assertStringContainsString(
            '$__tintin_loop_2 = true; foreach ($group["members"] as $member): $__tintin_loop_2 = false;',
            $render
        );
        $this->assertStringContainsString('<?php foreach ($others as $other): ?>', $render);
        $this->assertSame(2, substr_count($render, '<?php endif; ?>'));
        $this->assertSame(1, substr_count($render, '<?php endforeach; ?>'));

        $output = $this->evaluate($render, [
            'groups' => [
                ['name' => 'a', 'members' => ['x', 'y']],
                ['name' => 'b', 'members' => []],
            ],
            'others' => ['z'],
        ]);

        $this->assertSame('[axy] [bempty-group ] z', $output);

        $output = $this->evaluate($render, ['groups' => [], 'others' => []]);

        $this->assertSame('no-groups', $output);
    }

    /**
     * A bare %empty on a single line with its loop compiles like the multi-line form.
     */
    public function testCompileInlineLoopEmpty()
    {
        $source = '%loop ($items as $item) <b>{{ $item }}</b> %empty <i>none</i> %endloop';

        $render = $this->compiler->compile($source);

        $this->assertSame('<b>a</b> <b>b</b>', $this->evaluate($render, ['items' => ['a', 'b']]));
        $this->assertSame('<i>none</i>', $this->evaluate($render, ['items' => []]));
    }

    /**
     * %empty($x) with parentheses is still the conditional helper, even inside a %loop.
     */
    public function testEmptyHelperInsideLoopIsNotTreatedAsLoopBranch()
    {
        $source = "%loop(\$users as \$user)\n%empty(\$user)\n-\n%endempty\n{{ \$user }}\n%endloop";

        $render = $this->compiler->compile($source);

        $this->assertStringContainsString('<?php foreach ($users as $user): ?>', $render);
        $this->assertStringContainsString('<?php if (empty($user)): ?>', $render);
        $this->assertStringContainsString('<?php endif; ?>', $render);
        $this->assertStringContainsString('<?php endforeach; ?>', $render);
        $this->assertStringNotContainsString('__tintin_loop', $render);

        $this->assertSame('- a', $this->evaluate($render, ['users' => ['', 'a']]));
    }

    /**
     * Compiling twice with the same compiler instance must not leak loop state.
     */
    public function testCompileLoopEmptyStateIsResetBetweenCompiles()
    {
        $this->compiler->compile("%loop(\$a as \$b)\n%empty\n%endloop");

        $render = $this->compiler->compile("%loop(\$a as \$b)\n%endloop");

        $this->assertStringContainsString('<?php foreach ($a as $b): ?>', $render);
        $this->assertStringContainsString('<?php endforeach; ?>', $render);
        $this->assertStringNotContainsString('__tintin_loop', $render);
    }

    /**
     * %loop head variants: no space, key/value pairs, surrounding whitespace.
     */
    public function testCompileForeachHeadVariants()
    {
        $compile_foreach = $this->makeReflectionFor('compileForeach');

        $render = $compile_foreach->invoke($this->compiler, '%loop($names as $name)');
        $this->assertEquals('<?php foreach ($names as $name): ?>', $render);

        $render = $compile_foreach->invoke($this->compiler, '%loop($users as $id => $user)');
        $this->assertEquals('<?php foreach ($users as $id => $user): ?>', $render);

        $render = $compile_foreach->invoke($this->compiler, '%loop($user->posts() as [$title, $body])');
        $this->assertEquals('<?php foreach ($user->posts() as [$title, $body]): ?>', $render);

        $render = $compile_foreach->invoke($this->compiler, '  %loop ($a as $b)  ');
        $this->assertEquals('  <?php foreach ($a as $b): ?>', $render);
    }

    /**
     * Each loop compiler returns an empty string when its directive is absent,
     * so the stack runner knows to keep the previous expression.
     */
    public function testLoopCompilersReturnEmptyStringWhenNothingMatches()
    {
        $methods = [
            'compileForeach', 'compileEndForeach', 'compileWhile', 'compileEndWhile',
            'compileFor', 'compileEndFor', 'compileContinue', 'compileBreak', 'compileLoopEmpty',
        ];

        foreach ($methods as $method) {
            $render = $this->makeReflectionFor($method)->invoke($this->compiler, 'Hello {{ $name }}');

            $this->assertSame('', $render, "$method should ignore an expression without its directive");
        }
    }

    /**
     * %endloop, %endwhile and %endfor swallow the newlines around them.
     */
    public function testCompileLoopEndsStripSurroundingNewlines()
    {
        $cases = [
            'compileEndForeach' => ["\n\n%endloop\n", '<?php endforeach; ?>'],
            'compileEndWhile' => ["\n%endwhile\n\n", '<?php endwhile; ?>'],
            'compileEndFor' => ["\n%endfor\n", '<?php endfor; ?>'],
        ];

        foreach ($cases as $method => [$source, $expected]) {
            $render = $this->makeReflectionFor($method)->invoke($this->compiler, $source);

            $this->assertEquals($expected, $render, $method);
        }
    }

    /**
     * %while head variants: no space, nested parentheses in the condition.
     */
    public function testCompileWhileHeadVariants()
    {
        $compile_while = $this->makeReflectionFor('compileWhile');

        $render = $compile_while->invoke($this->compiler, '%while($i < 10)');
        $this->assertEquals('<?php while ($i < 10): ?>', $render);

        $render = $compile_while->invoke($this->compiler, '%while ($i < count($items) && !empty($items[$i]))');
        $this->assertEquals('<?php while ($i < count($items) && !empty($items[$i])): ?>', $render);

        $render = $compile_while->invoke($this->compiler, '%while (($row = $cursor->next()) !== null)');
        $this->assertEquals('<?php while (($row = $cursor->next()) !== null): ?>', $render);
    }

    /**
     * %for head variants: no space, function calls and nested parentheses.
     */
    public function testCompileForHeadVariants()
    {
        $compile_for = $this->makeReflectionFor('compileFor');

        $render = $compile_for->invoke($this->compiler, '%for($i = 0; $i < 10; $i++)');
        $this->assertEquals('<?php for ($i = 0; $i < 10; $i++): ?>', $render);

        $render = $compile_for->invoke($this->compiler, '%for ($i = 0, $n = count($items); $i < $n; $i += 2)');
        $this->assertEquals('<?php for ($i = 0, $n = count($items); $i < $n; $i += 2): ?>', $render);

        $render = $compile_for->invoke($this->compiler, '%for (;;)');
        $this->assertEquals('<?php for (;;): ?>', $render);
    }

    /**
     * %stop compiles to break, bare or guarded by a condition.
     */
    public function testCompileStop()
    {
        $compile_break = $this->makeReflectionFor('compileBreak');

        $render = $compile_break->invoke($this->compiler, '%stop');
        $this->assertEquals('<?php break; ?>', $render);

        $render = $compile_break->invoke($this->compiler, '%stop ($name == "Tintin")');
        $this->assertEquals('<?php if ($name == "Tintin"): break; endif; ?>', $render);

        $render = $compile_break->invoke($this->compiler, '%stop($i > 10)');
        $this->assertEquals('<?php if ($i > 10): break; endif; ?>', $render);
    }

    /**
     * %jump compiles to continue, with or without a space before its condition.
     */
    public function testCompileJumpHeadVariants()
    {
        $compile_continue = $this->makeReflectionFor('compileContinue');

        $render = $compile_continue->invoke($this->compiler, '%jump($name == "Tintin")');
        $this->assertEquals('<?php if ($name == "Tintin"): continue; endif; ?>', $render);

        $render = $compile_continue->invoke($this->compiler, '%jump (in_array($name, $skipped))');
        $this->assertEquals('<?php if (in_array($name, $skipped)): continue; endif; ?>', $render);
    }

    /**
     * The breaker directives must not be confused with words that merely start
     * with them, such as a %stopwatch custom directive or plain text.
     */
    public function testBreakerDoesNotMatchInsideOtherWords()
    {
        $compile_break = $this->makeReflectionFor('compileBreak');
        $compile_continue = $this->makeReflectionFor('compileContinue');

        $this->assertSame('', $compile_break->invoke($this->compiler, 'nonstop'));
        $this->assertSame('', $compile_continue->invoke($this->compiler, 'highjump'));
    }

    /**
     * Regression: a guarded %stop / %jump followed by other parentheses on the
     * same line must stop at its own `)` instead of swallowing the rest.
     */
    public function testBreakerConditionStopsAtItsOwnParenthesis()
    {
        $compile_break = $this->makeReflectionFor('compileBreak');
        $compile_continue = $this->makeReflectionFor('compileContinue');

        $render = $compile_break->invoke($this->compiler, '%stop($b) <span><?php echo e($x); ?></span>');
        $this->assertEquals('<?php if ($b): break; endif; ?><span><?php echo e($x); ?></span>', $render);

        $render = $compile_continue->invoke($this->compiler, '%jump(in_array($b, ["x"])) {{ $b }} %jump($c)');
        $this->assertEquals(
            '<?php if (in_array($b, ["x"])): continue; endif; ?>{{ $b }} <?php if ($c): continue; endif; ?>',
            $render
        );
    }

    /**
     * The bare %empty compiler ignores the parenthesised helper form.
     */
    public function testCompileLoopEmptyIgnoresHelperForm()
    {
        $compile_loop_empty = $this->makeReflectionFor('compileLoopEmpty');

        $this->assertSame('', $compile_loop_empty->invoke($this->compiler, '%empty($users)'));
        $this->assertSame('', $compile_loop_empty->invoke($this->compiler, '%empty ($users)'));
        $this->assertSame('', $compile_loop_empty->invoke($this->compiler, '%endempty'));
        $this->assertSame('', $compile_loop_empty->invoke($this->compiler, '%notempty($users)'));
    }

    /**
     * The bare %empty swallows the newlines around it like the other branch directives.
     */
    public function testCompileLoopEmptyStripsSurroundingNewlines()
    {
        $compile_loop_empty = $this->makeReflectionFor('compileLoopEmpty');

        $render = $compile_loop_empty->invoke($this->compiler, "\n%empty\n");

        $this->assertEquals('<?php endforeach; if ($__tintin_loop_1): ?>', $render);
    }

    /**
     * The pre-scan records, in document order, each loop's depth and whether it
     * owns an %empty branch, each branch's depth, and each end's branch flag.
     */
    public function testScanLoopEmptyRecordsNestedStructure()
    {
        $scan = $this->makeReflectionFor('scanLoopEmpty');

        $scan->invoke($this->compiler, implode("\n", [
            '%loop($groups as $group)',
            '%loop($group as $member)',
            '%empty',
            '%endloop',
            '%empty',
            '%endloop',
            '%loop($others as $other)',
            '%endloop',
        ]));

        $this->assertSame([[1, true], [2, true], [1, false]], $this->readProperty('loop_empty_heads'));
        $this->assertSame([2, 1], $this->readProperty('loop_empty_branches'));
        $this->assertSame([true, true, false], $this->readProperty('loop_empty_ends'));
    }

    /**
     * The pre-scan ignores the %empty(...) helper, a stray %empty or %endloop
     * outside any loop, and %while / %for which have no empty branch.
     */
    public function testScanLoopEmptyIgnoresHelperAndStrayTokens()
    {
        $scan = $this->makeReflectionFor('scanLoopEmpty');

        $scan->invoke($this->compiler, implode("\n", [
            '%empty',
            '%endloop',
            '%loop($users as $user)',
            '%empty($user)',
            '%endempty',
            '%notempty($user)',
            '%endnotempty',
            '%endloop',
            '%while($i < 3)',
            '%empty',
            '%endwhile',
        ]));

        $this->assertSame([[1, false]], $this->readProperty('loop_empty_heads'));
        $this->assertSame([], $this->readProperty('loop_empty_branches'));
        $this->assertSame([false], $this->readProperty('loop_empty_ends'));
    }

    /**
     * The pre-scan starts from a clean slate on every call.
     */
    public function testScanLoopEmptyResetsPreviousState()
    {
        $scan = $this->makeReflectionFor('scanLoopEmpty');

        $scan->invoke($this->compiler, "%loop(\$a as \$b)\n%empty\n%endloop");
        $scan->invoke($this->compiler, "%loop(\$a as \$b)\n%endloop");

        $this->assertSame([[1, false]], $this->readProperty('loop_empty_heads'));
        $this->assertSame([], $this->readProperty('loop_empty_branches'));
        $this->assertSame([false], $this->readProperty('loop_empty_ends'));
    }

    /**
     * The loop stack runner applies every loop directive found on one line.
     */
    public function testCompileLoopStackAppliesAllDirectives()
    {
        $compile_stack = $this->makeReflectionFor('compileLoopStack');

        $render = $compile_stack->invoke(
            $this->compiler,
            '%loop($a as $b) %stop($b) %endloop %while($x) %jump %endwhile %for($i = 0; $i < 1; $i++) %endfor'
        );

        $this->assertStringContainsString('<?php foreach ($a as $b): ?>', $render);
        $this->assertStringContainsString('<?php if ($b): break; endif; ?>', $render);
        $this->assertStringContainsString('<?php endforeach; ?>', $render);
        $this->assertStringContainsString('<?php while ($x): ?>', $render);
        $this->assertStringContainsString('<?php continue; ?>', $render);
        $this->assertStringContainsString('<?php endwhile; ?>', $render);
        $this->assertStringContainsString('<?php for ($i = 0; $i < 1; $i++): ?>', $render);
        $this->assertStringContainsString('<?php endfor; ?>', $render);
        $this->assertStringNotContainsString('%', $render);
    }

    /**
     * The loop stack runner leaves a line without loop directives untouched.
     */
    public function testCompileLoopStackLeavesOtherLinesUntouched()
    {
        $compile_stack = $this->makeReflectionFor('compileLoopStack');

        $line = '<p>Hello {{ $name }}</p> %if($x) %endif';

        $this->assertSame($line, $compile_stack->invoke($this->compiler, $line));
    }

    /**
     * End-to-end: %loop with key/value pairs renders every pair.
     */
    public function testRenderForeachWithKeyValue()
    {
        $render = $this->compiler->compile("%loop(\$users as \$id => \$user)\n{{ \$id }}={{ \$user }};\n%endloop");

        $this->assertSame('1=a; 2=b;', $this->evaluate($render, ['users' => [1 => 'a', 2 => 'b']]));
    }

    /**
     * End-to-end: %while runs until its condition becomes false.
     */
    public function testRenderWhile()
    {
        $render = $this->compiler->compile("%while(\$i < 3)\n{{ \$i++ }}\n%endwhile");

        $this->assertSame('012', $this->evaluate($render, ['i' => 0]));
    }

    /**
     * End-to-end: %for with %jump and %stop skips one iteration and exits early.
     */
    public function testRenderForWithJumpAndStop()
    {
        $render = $this->compiler->compile(implode("\n", [
            '%for($i = 0; $i < 10; $i++)',
            '%jump($i == 1)',
            '%stop($i == 3)',
            '{{ $i }}',
            '%endfor',
        ]));

        $this->assertSame('02', $this->evaluate($render, []));
    }

    /**
     * End-to-end: bare %stop and %jump inside %if blocks within a %loop.
     */
    public function testRenderLoopWithBareJumpAndStop()
    {
        $render = $this->compiler->compile(implode("\n", [
            '%loop($names as $name)',
            '%if($name == "skip")',
            '%jump',
            '%endif',
            '%if($name == "end")',
            '%stop',
            '%endif',
            '{{ $name }},',
            '%endloop',
        ]));

        $output = $this->evaluate($render, ['names' => ['a', 'skip', 'b', 'end', 'c']]);

        $this->assertSame('a, b,', $output);
    }

    /**
     * End-to-end: %stop inside a loop that has an %empty branch does not
     * trigger the branch, since the body ran at least once.
     */
    public function testRenderLoopEmptyNotTriggeredAfterStop()
    {
        $render = $this->compiler->compile(implode("\n", [
            '%loop($names as $name)',
            '{{ $name }}',
            '%stop',
            '%empty',
            'none',
            '%endloop',
        ]));

        $this->assertSame('a', $this->evaluate($render, ['names' => ['a', 'b']]));
        $this->assertSame('none', $this->evaluate($render, ['names' => []]));
    }

    /**
     * Read a private property of the compiler under test.
     */
    private function readProperty(string $name)
    {
        $property = new ReflectionProperty(Compiler::class, $name);
        $property->setAccessible(true);

        return $property->getValue($this->compiler);
    }

    /**
     * Run compiled PHP with the given variables and return its output with
     * whitespace collapsed, since PHP drops the newline that follows `?>`.
     */
    private function evaluate(string $php, array $data): string
    {
        extract($data);
        ob_start();
        eval('?>' . $php);

        return trim(preg_replace('/\s+/', ' ', ob_get_clean()));
    }
}
