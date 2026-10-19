<?php

namespace Tintin\Lexique;

trait CompileLoop
{
    /**
     * The name of the runtime flag that records whether a %loop body ran.
     *
     * @var string
     */
    private string $loop_empty_flag = '$__tintin_loop_{depth}';

    /**
     * For each %loop in the template (document order): its nesting depth and
     * whether it owns a bare %empty branch, as [depth, has_empty].
     *
     * @var array<int, array{0: int, 1: bool}>
     */
    private array $loop_empty_heads = [];

    /**
     * For each bare %empty in the template (document order): the nesting depth of its loop.
     *
     * @var int[]
     */
    private array $loop_empty_branches = [];

    /**
     * For each %endloop in the template (document order): does its loop own a bare %empty?
     *
     * @var bool[]
     */
    private array $loop_empty_ends = [];

    /**
     * Definition of all available stack
     * @return array
     */
    private function getLoopStack(): array
    {
        return [
            'Foreach',
            'LoopEmpty',
            'EndForeach',
            'Continue',
            'Break',
            'While',
            'EndWhile',
            'For',
            'EndFor'
        ];
    }

    /**
     * Compile the loop directive stack
     *
     * @param string $expression
     * @return string
     */
    protected function compileLoopStack(string $expression): string
    {
        foreach ($this->getLoopStack() as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Pre-scan the template for %loop / %empty / %endloop so that each
     * directive knows, when it is compiled line by line, whether its loop owns
     * an %empty branch and at which nesting depth it sits.
     *
     * Only a bare `%empty` (no parentheses) is a loop branch; `%empty($x)` is
     * the conditional helper and is left to the helpers stack.
     *
     * @param string $data
     * @return void
     */
    protected function scanLoopEmpty(string $data): void
    {
        $this->loop_empty_heads = [];
        $this->loop_empty_branches = [];
        $this->loop_empty_ends = [];

        preg_match_all('/%(empty\b(?!\s*\()|endloop|loop\b)/', $data, $matches);

        $stack = [];

        foreach ($matches[1] as $token) {
            if ($token === 'loop') {
                $stack[] = count($this->loop_empty_heads);
                $this->loop_empty_heads[] = [count($stack), false];
                continue;
            }

            if (count($stack) === 0) {
                continue;
            }

            if ($token === 'empty') {
                $this->loop_empty_heads[end($stack)][1] = true;
                $this->loop_empty_branches[] = count($stack);
                continue;
            }

            $this->loop_empty_ends[] = $this->loop_empty_heads[array_pop($stack)][1];
        }
    }

    /**
     * Build the empty flag name for the given nesting depth
     *
     * @param int $depth
     * @return string
     */
    private function loopEmptyFlag(int $depth): string
    {
        return str_replace('{depth}', (string) $depth, $this->loop_empty_flag);
    }

    /**
     * Compile the %loop directive
     *
     * @param string $expression
     * @param string $lexic
     * @param string $o_lexic
     * @return string
     */
    private function compileLoop(string $expression, string $lexic, string $o_lexic): string
    {
        $regex = sprintf($this->condition_pattern, $lexic);

        $output = preg_replace_callback($regex, function ($match) use ($o_lexic) {
            array_shift($match);

            return "<?php $o_lexic ({$match[1]}): ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %endloop directive
     *
     * @param string $expression
     * @param string $lexic
     * @param string $o_lexic
     * @return string
     */
    private function compileEndLoop($expression, $lexic, $o_lexic): string
    {
        $output = preg_replace_callback("/\n*$lexic\n*/", function () use ($o_lexic) {
            return "<?php $o_lexic; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the loop breaker directive
     *
     * The optional condition uses a recursive balanced-paren matcher so that
     * `%stop($x) ... ($y)` on one line stops at the condition's own `)`.
     *
     * @param string $expression
     * @param string $lexic
     * @param string $o_lexic
     * @return string
     */
    private function compileBreaker($expression, $lexic, $o_lexic): string
    {
        $output = preg_replace_callback(
            "/($lexic\s*(\((?:[^()]|(?2))*\))\s*|$lexic)/s",
            function ($match) use ($lexic, $o_lexic) {
                array_shift($match);

                if (trim($match[0]) == $lexic) {
                    return "<?php $o_lexic; ?>";
                }

                return "<?php if {$match[1]}: $o_lexic; endif; ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %loop directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileForeach(string $expression): string
    {
        $regex = sprintf($this->condition_pattern, '%loop');

        $output = preg_replace_callback($regex, function ($match) {
            [$depth, $has_empty] = array_shift($this->loop_empty_heads) ?? [1, false];

            if (!$has_empty) {
                return "<?php foreach ({$match[2]}): ?>";
            }

            $flag = $this->loopEmptyFlag($depth);

            return "<?php $flag = true; foreach ({$match[2]}): $flag = false; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the bare %empty directive inside a %loop
     *
     * Closes the foreach and opens the branch rendered when the loop body never ran.
     * `%empty(...)` with parentheses is not matched here, it is the conditional helper.
     *
     * @param string $expression
     * @return string
     */
    protected function compileLoopEmpty(string $expression): string
    {
        $output = preg_replace_callback('/\n*%empty\b(?!\s*\()\n*/', function () {
            $depth = array_shift($this->loop_empty_branches) ?? 1;

            return "<?php endforeach; if ({$this->loopEmptyFlag($depth)}): ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %while directive
     *
     * @param $expression
     * @return string
     */
    protected function compileWhile(string $expression): string
    {
        return $this->compileLoop($expression, '%while', 'while');
    }

    /**
     * Compile the %for directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileFor(string $expression): string
    {
        return $this->compileLoop($expression, '%for', 'for');
    }

    /**
     * Compile the %endloop directive
     *
     * @param $expression
     * @return string
     */
    protected function compileEndForeach(string $expression): string
    {
        $output = preg_replace_callback('/\n*%endloop\n*/', function () {
            $has_empty = array_shift($this->loop_empty_ends) ?? false;

            return $has_empty ? "<?php endif; ?>" : "<?php endforeach; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %endwhile directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndWhile(string $expression): string
    {
        return $this->compileEndLoop($expression, '%endwhile', 'endwhile');
    }

    /**
     * Compile the %endfor directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndFor(string $expression): string
    {
        return $this->compileEndLoop($expression, '%endfor', 'endfor');
    }

    /**
     * Compile the %jump directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileContinue(string $expression): string
    {
        return $this->compileBreaker($expression, '%jump', 'continue');
    }

    /**
     * Compile the %stop directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileBreak(string $expression): string
    {
        return $this->compileBreaker($expression, '%stop', 'break');
    }
}
