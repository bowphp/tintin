<?php

namespace Tintin\Lexique;

trait CompileCustomDirective
{
    /**
     * Compile custom token stack
     * 
     * @param string $expression
     * @return mixed
     */
    protected function compileCustomStack(string $expression)
    {
        foreach (['Csrf'] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the csrf token
     * 
     * @param string $expression
     * @return string
     */
    protected function compileCsrf(string $expression)
    {
        $output = preg_replace_callback('/\n*(#csrf)\n*/', function ($match) {
            array_shift($match);

            return "<?= csrf_field(); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the custom statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileCustomDirective(string $expression)
    {
        $output = preg_replace_callback($this->getCustomDirectivePartern(), function ($match) {
            $value = $match[0];
            $name = $match[1];

            if (!isset($this->directives[$name])) {
                return $value;
            }

            $directive = $this->directives[$name];

            if ($directive['broken']) {
                return $this->_____executeCustomDirectory(
                    $name,
                    isset($match[3]) ? $match[3] : null
                );
            }

            $params = isset($match[3]) ? $match[3] : 'null';

            return "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\", $params);";
        }, $expression);

        return is_null($output) ? $expression : $output;
    }

    /**
     * Get partern
     * @return string
     */
    private function getCustomDirectivePartern()
    {
        return "/\n*\#([a-zA-Z_]+)\s*(\((.+?)?\)\n?)?/";
    }
}
