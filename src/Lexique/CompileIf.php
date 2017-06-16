<?php
namespace Tintin\Lexique;

use function var_dump;

trait CompileIf
{
    /**
     * @param $expression
     */
    protected function compileIfStack($expression)
    {
        foreach (['UnLess', 'If', 'ElseIf', 'Else', 'EndIf'] as $token) {
            $out = $this->{'compile'.$token}($expression);
            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }
        return $expression;
    }

    /**
     * @param $expression
     * @param $lexic
     * @param $o_lexic
     * @return mixed|string
     */
    private function compileIfStatement($expression, $lexic, $o_lexic)
    {
        $regex = sprintf($this->conditionPatern, $lexic);
        var_dump($regex);
        $output = preg_replace_callback($regex, function($match) use ($o_lexic, $lexic) {
            var_dump($match);
            array_shift($match);
            if ($lexic == '#unless') {
                return "<?php $o_lexic (! ({$match[1]})): ?>";
            } else {
                return "<?php $o_lexic ({$match[1]}): ?>";
            }
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return $this->compileIfStatement($expression, '#if', 'if');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileUnLess($expression)
    {
        return $this->compileIfStatement($expression, '#unless', 'if');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElse($expression)
    {
        $output = preg_replace_callback('/\n*#else\n*/', function() {
            return "<?php esle: ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileElseIf($expression)
    {
        return $this->compileIfStatement($expression, '#elseif', 'elseif');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndIf($expression)
    {
        $output = preg_replace_callback('/\n*(#endif|#endunless)\n*/', function($match) {
            array_shift($match);
            return "<?php endif; ?>";
        }, $expression);
        return $output == $expression ? '' : $output;
    }
}