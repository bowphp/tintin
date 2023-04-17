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
    public function make(string $template)
    {
        $__template = $template;
        $loader = $this->tintin->getLoader();

        if (is_null($loader)) {
            return $this->tintin->renderString($__template);
        }

        if (!$loader->exists($__template)) {
            $loader->failLoading($__template . ' macro is not found');
        }

        $__tintin = $this->tintin;

        /**
         * Load template when is not a cached file
         */
        if (!$loader->isExpired($__template)) {
            require $loader->getCacheFileResolvedPath($__template);
            return;
        }

        /**
         * Put the template into cache
         */
        $content = $loader->getFileContent($__template);

        $this->tintin->getCompiler()->compileMacroExtraction($content);
        $containers = $this->tintin->getCompiler()->getMacroContainers();
        $result = '';

        foreach ($containers as $name => $container) {
            $result .= $this->makeTheMocra($name, $container["parameters"], $container["content"]);
        }

        $result = "<?php\n\n" . $result;
        $loader->cache($__template, $result);

        require $loader->getCacheFileResolvedPath($__template);
    }

    /**
     * Create the macro as php function
     *
     * @param string $function
     * @param array $parameters
     * @param string $content
     * @return string
     */
    private function makeTheMocra(
        string $function,
        array $parameters,
        string $content
    ): string {
        $content = addcslashes($content, "'");
        return sprintf(
            "if (!function_exists('%s')) {\n\tfunction %s(%s)\n\t{\n\t\t%s\n\t\treturn \"\n\" . \$tintin->renderstring('%s', get_defined_vars()) . \"\n\";\n\t}\n}\n\n",
            $function,
            $function,
            implode(', ', $parameters),
            "\$tintin = new \Tintin\Tintin();",
            trim($content)
        );
    }
}
