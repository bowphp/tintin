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
            [$functions, $contents] = $matches;
            foreach($functions as $key => $function) {
                $partials = preg_split("/\s*,\s*/", $function);
                $name = current($partials);
                array_shift($partials);
                $parameters = $partials;
                $name = trim($name);
                $name = trim($name, '"\'');
                $content = $contents[$key];
                $this->macros[$name] = compact('parameters', 'content');
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

