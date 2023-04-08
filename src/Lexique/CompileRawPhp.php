<?php

namespace Tintin\Lexique;

trait CompileRawPhp
{
    /**
     * Compile the %raw...%endraw statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileRawPhp(string $expression): string
    {
        $expression = trim($expression);

        $output = preg_replace_callback(
            '/\n*\%raw\s*(\n*(?:.+?)\n*)\%endraw\n*/m',
            function ($match) {
                array_shift($match);

                return "<?php " . trim($match[0]) . " ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }
}
