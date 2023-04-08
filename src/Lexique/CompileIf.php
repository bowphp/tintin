<?php

namespace Tintin\Lexique;

trait CompileIf
{
    /**
     * Compile the if statement stack
     *
     * @param string $expression
     * @return string
     */
    protected function compileIfStack(string $expression): string
    {
        foreach (['UnLess', 'If', 'ElseIf', 'ElseIfAlias', 'Else', 'EndIf'] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %if statement
     *
     * Note: $o_lexic is the original PHP lexique
     *
     * @param string $expression
     * @param string $lexic
     * @param string $o_lexic
     *
     * @return string
     */
    private function compileIfStatement(string $expression, string $lexic, string $o_lexic): string
    {
        $regex = sprintf($this->condition_pattern, $lexic);

        $output = preg_replace_callback($regex, function ($match) use ($o_lexic, $lexic) {
            array_shift($match);

            if ($lexic == '%unless') {
                return "<?php $o_lexic (! ({$match[1]})): ?>";
            }

            if ($lexic == '%isset') {
                return "<?php $o_lexic (isset({$match[1]})): ?>";
            }

            return "<?php $o_lexic ({$match[1]}): ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %if statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileIf(string $expression): string
    {
        return $this->compileIfStatement($expression, '%if', 'if');
    }

    /**
     * Compile the %unless statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileUnLess(string $expression): string
    {
        return $this->compileIfStatement($expression, '%unless', 'if');
    }

    /**
     * Compile the %else statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileElse(string $expression): string
    {
        $output = preg_replace_callback('/\n*%else\n*/', function () {
            return "<?php else: ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %elseif statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileElseIf(string $expression): string
    {
        return $this->compileIfStatement($expression, '%elseif', 'elseif');
    }

    /**
     * Compile the %elseif statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileElseIfAlias(string $expression): string
    {
        return $this->compileIfStatement($expression, '%elif', 'elseif');
    }

    /**
     * Compile the %endif statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndIf(string $expression): string
    {
        $output = preg_replace_callback('/\n*(%endif|%endisset|%endunless)\n*/', function ($match) {
            array_shift($match);

            return "<?php endif; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
