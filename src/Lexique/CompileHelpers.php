<?php

namespace Tintin\Lexique;

trait CompileHelpers
{
    /**
     * Compile the if statement stack
     *
     * @param string $expression
     * @return string
     */
    protected function compileHelpersStack(string $expression): string
    {
        foreach (['Auth', 'EndAuth'] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %auth statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileAuth(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%auth');
    }

    /**
     * Compile the %endif statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndAuth(string $expression): string
    {
        $output = preg_replace_callback('/\n*(%endauth)\n*/', function ($match) {
            array_shift($match);

            return "<?php endif; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %if statement
     *
     * Note: $o_lexic is the original PHP lexique
     *
     * @param string $expression
     * @param string $lexic
     *
     * @return string
     */
    private function compileAuthStatement(string $expression, string $lexic): string
    {
        $regex = sprintf($this->option_condition_pattern, $lexic);
        
        $output = preg_replace_callback($regex, function ($match) use ($lexic, $expression) {
            array_shift($match);

            $params = count($match) > 2 ? end($match) : '';

            if ($lexic == '%auth') {
                return "<?php if (auth(". $params .")->check()): ?>";
            }

            return $expression;
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
