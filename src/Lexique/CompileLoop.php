<?php
namespace Tintin\Lexique;

trait CompileLoop
{
    /**
     * @param $expression
     */
    protected function compileLoopStack($expression)
    {
        foreach ($this->getLoopStack() as $token) {
            $out = $this->{'compile'.$token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * @return array
     */
    private function getLoopStack()
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
     * @param $expression
     * @param $lexic
     * @param $o_lexic
     * @return mixed|string
     */
    private function compileLoop($expression, $lexic, $o_lexic)
    {
        $regex = sprintf($this->conditionPatern, $lexic);

        $output = preg_replace_callback($regex, function($match) use ($o_lexic) {
            array_shift($match);

            return "<?php $o_lexic ({$match[1]}): ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @param $lexic
     * @param $o_lexic
     * @return mixed|string
     */
    private function compileEndLoop($expression, $lexic, $o_lexic)
    {
        $output = preg_replace_callback("/\n*$lexic\n*/", function() use ($o_lexic) {
            return "<?php $o_lexic; ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @param $lexic
     * @param $o_lexic
     * @return mixed|string
     */
    private function compileBreaker($expression, $lexic, $o_lexic)
    {
        $output = preg_replace_callback("/($lexic *(\(.+?\))|$lexic)/s", function($match) use ($lexic, $o_lexic) {
            array_shift($match);

            if ($match[0] == $lexic) {
                return "<?php $o_lexic; ?>";
            } else {
                return "<?php if ({$match[1]}): $o_lexic; endif;?>";
            }
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        return $this->compileLoop($expression, '#loop', 'foreach');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return $this->compileLoop($expression, '#while', 'while');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return $this->compileLoop($expression, '#for', 'for');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndForeach($expression)
    {
        return $this->compileEndLoop($expression, '#endloop', 'endforeach');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndWhile($expression)
    {
        return $this->compileEndLoop($expression, '#endwhile', 'endwhile');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileEndFor($expression)
    {
        return $this->compileEndLoop($expression, '#endfor', 'endfor');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileContinue($expression)
    {
        return $this->compileBreaker($expression, '#jump', 'continue');
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileBreak($expression)
    {
        return $this->compileBreaker($expression, '#stop', 'break');
    }
}