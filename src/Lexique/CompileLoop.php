<?php
namespace Tintin\Lexique;

trait CompileLoop
{
    /**
     * @param $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        $regex = sprintf($this->conditionPatern, '@loop');
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return "<?php foreach {$match[1]}: ?>\n";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndForeach($expression)
    {
        $output = preg_replace_callback('/\n*@endloop\n*/', function() {
            return "\n<?php endforeach; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileContinue($expression)
    {
        $output = preg_replace_callback('/@jump *?=[^(]/s', function() {
            return "<?php continue; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileContinueIf($expression)
    {
        $regex = sprintf($this->conditionPatern, '@jump');
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return "<?php if {$match[1]}: continue; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileBreak($expression)
    {
        $output = preg_replace_callback('/@stop *?=[^(]/s', function() {
            return "<?php break; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileBreakIf($expression)
    {
        $regex = sprintf($this->conditionPatern, '@stop');
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return "<?php if '.$match[1].': break; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }
}