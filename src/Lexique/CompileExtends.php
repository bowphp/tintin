<?php

namespace Tintin\Lexique;

trait CompileExtends
{
    /**
     * Compile the inherit concept directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtendsStack(string $expression): string
    {
        foreach (['Block', 'EndBlock', 'ConditionalInclude', 'Include', 'Inject', 'Extends'] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %block directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileBlock($expression): string
    {
        $output = preg_replace_callback(
            "/\n*\%block\s*\((.+?)(?:,(.+?))?\)\n*/m",
            function ($match) {
                array_shift($match);

                $content = null;

                if (count($match) == 2) {
                    $content = $match[1];
                }

                if (is_null($content)) {
                    return "<?php \$__tintin->getStackManager()->startStack({$match[0]}); ?>";
                } else {
                    return "<?php \$__tintin->getStackManager()->startStack({$match[0]}, $content); ?>";
                }
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %endblock directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndBlock(string $expression): string
    {
        $output = preg_replace_callback("/\n*%endblock\n*/m", function () {
            return "<?php \$__tintin->getStackManager()->endStack(); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %include directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileInclude(string $expression): string
    {
        $regex = "/\%include\s*\(((?:\n|\s|\t)*(?:.+)(?:\n|\s|\t)*\)?)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            return "<?php echo \$__tintin->getStackManager()->includeFile({$match[1]}, ['__tintin' => \$__tintin]); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %include directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileConditionalInclude(string $expression): string
    {
        $regex = "/\%include(If|When)\s*\(((?:\n|\s|\t)*(?:.+)(?:\n|\s|\t)*\)?)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            array_shift($match);
            [$type, $params] = $match;
            return "<?php echo \$__tintin->getStackManager()->includeFile{$type}({$params}, ['__tintin' => \$__tintin]); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %extends directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtends(string $expression): string
    {
        $regex = "/^\%extends\s*\(((?:\n|\s|\t)*(?:.+)(?:\n|\s|\t)*\)?)\)/sm";

        if (preg_match($regex, $expression, $match)) {
            $this->extends_render[] = "<?php echo \$__tintin->getStackManager()->includeFile({$match[1]}, ['__tintin' => \$__tintin]); ?>";

            return ' ';
        }

        return $expression;
    }

    /**
     * Compile the %inject directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileInject(string $expression): string
    {
        $regex = "/\%inject\s*\(((?:\n|\s|\t)*(?:.+?)(?:\n|\s|\t)*)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            return "<?php echo \$__tintin->getStackManager()->getStack({$match[1]}); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
