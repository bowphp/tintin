<?php

namespace Tintin\Lexique;

trait CompileRawPhp
{
    /**
     * Compile the %raw...%endraw directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileRawPhp(string $expression): string
    {
        // $expression = trim($expression);

        foreach (['Raw', 'EndRaw'] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %raw...%endraw directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileRaw(string $expression): string
    {
        // $expression = trim($expression);

        $output = preg_replace_callback(
            '/\%raw/',
            function ($match) {
                array_shift($match);

                return "<?php";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %raw...%endraw directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndRaw(string $expression): string
    {
        // $expression = trim($expression);

        $output = preg_replace_callback(
            '/\%endraw/',
            function ($match) {
                array_shift($match);

                return "?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }
}
