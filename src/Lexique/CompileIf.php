<?php
namespace Tintin\Lexique;

trait CompileIf
{
    /**
     * @param $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        $regex = sprintf($this->conditionPatern, '@if');
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return "\n<?php if {$match[1]}: ?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElse($expression)
    {
        $output = preg_replace_callback('/\n*@else:\n*/', function() {
            return "\n<?php esle: ?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElseIf($expression)
    {
        $regex = sprintf($this->conditionPatern, '@elseif');
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return "\n<?php elseif {$match[1]}: ?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndIf($expression)
    {
        $output = preg_replace_callback('/\n*@endif\n*/', function($match) {
            array_shift($match);
            return "\n<?php endif; ?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }
}