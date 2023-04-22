<?php

namespace Tintin\Lexique;

trait CompileCustomDirective
{
    /**
     * Compile the custom directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileCustomDirective(string $expression): string
    {
        $output = preg_replace_callback($this->getCustomDirectivePartern(), function ($match) {
            [$value, $name] = $match;

            if (!isset($this->directives[$name])) {
                return $value;
            }

            $directive = $this->directives[$name];

            if ($directive['broken']) {
                return $this->_____executeCustomDirectory(
                    $name,
                    $match[3] ?? []
                );
            }

            $params = $match[3] ?? null;

            if (is_null($params) || strlen(trim($params)) === 0) {
                return "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\");";
            }

            return "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\", $params);";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Get partern
     * @return string
     */
    private function getCustomDirectivePartern()
    {
        return "/^%([a-zA-Z_]+)\s*(\((.*?)\))?$/sm";
    }
}
