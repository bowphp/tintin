<?php
namespace Tintin\Lexique;

trait CompileRawPhp
{
    /**
     * @param $expression
     * @return mixed|string
     */
    protected function compileRawPhp($expression)
    {
        $output = preg_replace_callback('/(?:@raw(.+)@endraw)/sm', function($match) {
            array_shift($match);
            var_dump($match);
            return "\n<?php\n{$match[1]}\n?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }
}