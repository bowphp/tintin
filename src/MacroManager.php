<?php

namespace Tintin;

class MacroManager
{
    /**
     * The Tintin instance
     *
     * @var Tintin
     */
    private Tintin $tintin;

    /**
     * StackManager constructor.
     *
     * @param Tintin $tintin
     *
     * @return void
     */
    public function __construct(Tintin $tintin)
    {
        $this->tintin = $tintin;
    }

    /**
     * Make the macro bundle
     *
     * @param string $template
     * @return string
     */
    public function make(string $template): string
    {
        $loader = $this->tintin->getLoader();

        if (is_null($loader)) {
            return $this->tintin->renderString($template);
        }

        if (!$loader->exists($template)) {
            $loader->failLoading($template . ' macro is not found');
        }

        $__tintin = $this->tintin;

        /**
         * Load template when is not a cached file
         */
        if (!$loader->isExpired($template)) {
            require $loader->getCacheFileResolvedPath($template);
        }

        /**
         * Put the template into cache
         */
        $content = $loader->getFileContent($template);

        $this->tintin->getCompiler()->compileMacroExtraction($content);
        $containers = $this->tintin->getCompiler()->getMacroContainers();
        $result = '';

        foreach ($containers as $name => $container) {
            $result .= $this->makeMacro($name, $container["parameters"], $container["content"]);
        }

        $result = "<?php\n\n" . $result;
        $loader->cache($template, $result);

        require $loader->getCacheFileResolvedPath($template);
        return "";
    }

    /**
     * Create the macro as php function
     *
     * @param string $function
     * @param array $parameters
     * @param string $content
     * @return string
     */
    private function makeMacro(
        string $function,
        array $parameters,
        string $content
    ): string {
        $content = trim(addcslashes($content, "'"));
        // $parts = preg_split("/\n|\r\n/", $content);
        // $parts = array_map(fn($value) => trim($value), $parts);
        return sprintf(
            "if (!function_exists('%s')) {\n\tfunction %s(%s)\n\t{\n\t\t%s\n\t\treturn \$tintin->renderString('%s', get_defined_vars());\n\t}\n}\n\n",
            $function,
            $function,
            implode(', ', $parameters),
            "\$tintin = new \Tintin\Tintin();",
            trim($content)
        );
    }
}
