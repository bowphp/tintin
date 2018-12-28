<?php

namespace Tintin\Lexique;

trait CompileCustomDirective
{
    /**
     * Compile the {# commentes #} statement
     *
     * @param  string  $value
     * @return string
     */
    protected function compileCustomDirective($expression)
    {
        $output = preg_replace_callback($this->getCustomDirectivePartern(), function ($match) {
            $name = $match[1];

            if (!isset($this->directives[$name])) {
                return null;
            }

            if (count($match) == 4) {
                return "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\", $match[3]);";
            }

            return "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\");";
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
