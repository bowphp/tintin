<?php

namespace Tintin\Lexique;

trait CompileCustomDirective
{
    /**
     * Compile the custom statement
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileCustomDirective($expression)
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
     *
     * @return string
     */
    private function getCustomDirectivePartern()
    {
        return "/\n*\#([a-zA-Z_]+)\s*(\((.+?)?\)\n?)?/";
    }
}
