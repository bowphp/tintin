<?php

namespace Tintin\Lexique;

trait CompileMacro
{
    /**
     * The macro containers
     *
     * @var array
     */
    protected array $macros = [];

    /**
     * Get the macro containers
     *
     * @return array
     */
    public function getMacroContainers(): array
    {
        return $this->macros;
    }

    /**
     * Complie the %macro and %endmacro statements
     *
     * @param string $expression
     * @return string
     */
    public function compileMacroExtraction(string $expression): string
    {
        $regex = "/(?:%macro\s*\n*\((.*?)\)\n*\s*(.*?)%endmacro)/s";

        preg_match_all($regex, $expression, $matches);

        $macros = (array) array_shift($matches);

        if (count($matches) > 0) {
            [$definitions, $contents] = $matches;
            foreach ($definitions as $key => $definition) {
                $partials = preg_split("/\s*,\s*/", $definition);
                $function = current($partials);
                $function = trim(trim($function), '"\'');
                array_shift($partials);
                $parameters = $partials;
                $content = $contents[$key];
                $this->macros[$function] = compact('parameters', 'content');
            }
        }

        // Clear the %macro from expression
        foreach ($macros as $macro) {
            $expression = str_replace($macro, "", $expression);
            $expression = trim($expression);
        }

        return $expression;
    }

    /**
     * Compile the %import
     *
     * @param string $expression
     * @return string
     */
    public function compileImport(string $expression): string
    {
        $regex = "/%import\s*\n*\((.*?)\)/sm";

        if (!preg_match($regex, $expression, $match)) {
            return $expression;
        };

        $name = trim($match[1], '"\'');

        $this->imports_render[] =
            "<?php \$__tintin->getMacroManager()->make(\"{$name}\", ['__tintin' => \$__tintin]); ?>";

        return "";
    }
}
