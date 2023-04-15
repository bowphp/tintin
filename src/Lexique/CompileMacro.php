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
     * Complie the %macro and %endmacro statements
     *
     * @param string $expression
     * @return string
     */
    public function compileMacro(string $expression): string
    {
        $regex = "/(?:%macro\s*\n*\((.*?)\)\n*\s*(.*?)%endmacro)/s";

        preg_match_all($regex, $expression, $matches);

        array_shift($matches);

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

        return 'Hello {{ $name }}';
    }

    /**
     * Compile the %import
     *
     * @param string $expression
     * @return string
     */
    public function compileImport(string $expression): string
    {
        $regex = "/^%import\s*\n*\((.*?)\)$/sm";

        $output = preg_replace_callback($regex, function ($match) {
            $name = trim($match[1], '"\'');
            return "<?php echo \$__tintin->getMacroManager()->make(\"{$name}\", ['__tintin' => \$__tintin]); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
