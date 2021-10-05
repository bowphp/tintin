<?php

namespace Tintin\Lexique;

trait CompileEchos
{
    /**
     * Compile the echo statement concept
     *
     * @param string $expression
     * @return mixed
     */
    protected function compileEchoStack($expression)
    {
        foreach (['RawEcho', 'Echo'] as $token) {
            $out = $this->{'compile'.$token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the {{ $name }} statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEcho($expression)
    {
        $regex = sprintf(
            '/((?:%s\s*(.+?)\s*%s))+/',
            $this->echo_tags[0],
            $this->echo_tags[1]
        );

        $output = preg_replace_callback($regex, function ($match) use ($expression) {
            array_shift($match);
            $value = $match[0];

            if (preg_match("/{{\s*([a-z_#\/\^@]+[a-z0-9_]+)\s*}}/", $value)) {
                return $value;
            }
            
            return '<?php echo htmlspecialchars('.$match[1].', ENT_QUOTES); ?>';

        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the {{{ $name }}} statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileRawEcho($expression)
    {
        $regex = sprintf(
            '/((?:%s\s*(.+?)\s*%s))+/',
            $this->raw_echo_tags[0],
            $this->raw_echo_tags[1]
        );

        $output = preg_replace_callback($regex, function ($match) {
            array_shift($match);
            $value = $match[0];

            if (preg_match("/{{{\s*([a-z_#\/\^@]+[a-z0-9_]+)\s*}}}/", $value)) {
                return $value;
            }

            return '<?php echo '.$match[1].'; ?>';
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
