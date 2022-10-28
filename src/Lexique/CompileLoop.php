<?php

namespace Tintin\Lexique;

trait CompileLoop
{
    /**
     * Definition of all available stack
     * @return array
     */
    private function getLoopStack(): array
    {
        return [
            'Foreach',
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
     * Compile the loop statement stack
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
     * Compile the #loop statement
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
     * Compile the #endloop statement
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
     * Compile the loop breaker statement
     *
     * @param string $expression
     * @param string $lexic
     * @param string $o_lexic
     * @return string
     */
    private function compileBreaker($expression, $lexic, $o_lexic): string
    {
        $output = preg_replace_callback(
            "/($lexic\s*(\(.+\)\s*)|$lexic)/s",
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
     * Compile the #loop statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileForeach(string $expression): string
    {
        return $this->compileLoop($expression, '#loop', 'foreach');
    }

    /**
     * Compile the #while statement
     *
     * @param $expression
     * @return string
     */
    protected function compileWhile(string $expression): string
    {
        return $this->compileLoop($expression, '#while', 'while');
    }

    /**
     * Compile the #for statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileFor(string $expression): string
    {
        return $this->compileLoop($expression, '#for', 'for');
    }

    /**
     * Compile the #endloop statement
     *
     * @param $expression
     * @return string
     */
    protected function compileEndForeach(string $expression): string
    {
        return $this->compileEndLoop($expression, '#endloop', 'endforeach');
    }

    /**
     * Compile the #endwhile statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndWhile(string $expression): string
    {
        return $this->compileEndLoop($expression, '#endwhile', 'endwhile');
    }

    /**
     * Compile the #endfor statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndFor(string $expression): string
    {
        return $this->compileEndLoop($expression, '#endfor', 'endfor');
    }

    /**
     * Compile the #jump statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileContinue(string $expression): string
    {
        return $this->compileBreaker($expression, '#jump', 'continue');
    }

    /**
     * Compile the #stop statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileBreak(string $expression): string
    {
        return $this->compileBreaker($expression, '#stop', 'break');
    }
}
