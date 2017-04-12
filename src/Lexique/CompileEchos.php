<?php
namespace Tintin\Lexique;

trait CompileEchos
{
    /**
     * @param $expression
     * @return string
     */
    protected function compileEcho($expression)
    {
        $regex = sprintf('/((?:%s\s*(.+?)\s*%s))+/', $this->echoTags[0], $this->echoTags[1]);
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return '<?php echo '.$match[1].'; ?>';
        }, $expression);
        return $output == $expression ? '' : $output;
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileRawEcho($expression)
    {
        $regex = sprintf('/((?:%s\s*(.+?)\s*%s))+/s', $this->rawEchoTags[0], $this->rawEchoTags[1]);
        $output = preg_replace_callback($regex, function($match) {
            array_shift($match);
            return '<?php echo e('.$match[1].'); ?>';
        }, $expression);
        return $output == $expression ? '' : $output;
    }
}