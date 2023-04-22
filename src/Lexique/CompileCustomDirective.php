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
        $collection = [];
        preg_match_all($this->getCustomDirectivePartern(), $expression, $matches);

        array_shift($matches);
        $replaces = [];
        $values = $matches[0] ?? [];
        $names = $matches[1] ?? [];
        $parameters = $matches[2] ?? [];

        foreach ($values as $key => $value) {
            $name = $names[$key] ?? null;

            if (!isset($this->directives[$name])) {
                continue;
            }

            $replaces[$name] = $value;
            $directive = $this->directives[$name];
            $parameter = $parameters[$key] ?? null;

            if ($directive['broken']) {
                $collection[$name] = $this->_____executeCustomDirectory(
                    $name,
                    (is_null($parameter) || $parameter === false || strlen(trim($parameter)) === 0) ? $parameter : []
                );
                continue;
            }

            if (is_null($parameter) || $parameter === false || strlen(trim($parameter)) === 0) {
                $collection[$name] = "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\"); ?>";
            } else {
                $collection[$name] = "<?php echo \$__tintin->getCompiler()->_____executeCustomDirectory(\"$name\", $parameter); ?>";
            }
        }

        foreach ($replaces as $name => $value) {
            if (array_key_exists($name, $collection)) {
                $expression = str_replace($value, $collection[$name], $expression);
            }
        }

        return $expression;
    }

    /**
     * Get partern
     *
     * @return string
     */
    private function getCustomDirectivePartern()
    {
        return "/(%([a-zA-Z]+)\s*(?:\((.*?)\))?)/sm";
    }
}
