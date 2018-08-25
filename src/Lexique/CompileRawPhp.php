<?php

namespace Tintin\Lexique;

trait CompileRawPhp
{
    /**
     * Compile the #raw...#endraw statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileRawPhp($expression)
    {
        $output = preg_replace_callback('/\n*\#raw\s*(\n*(?:.+?)\n*)\#endraw\n*/sm', function ($match) {
            array_shift($match);

            return "\n<?php \n{$match[1]}\n?>\n";
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
