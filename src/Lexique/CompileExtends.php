<?php

namespace Tintin\Lexique;

trait CompileExtends
{
    use CompileStacks;

    /**
     * Compile the inherit concept statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtendsStack($expression)
    {
        foreach (['Include', 'Block', 'EndBlock', 'Inject', 'Extends'] as $token) {
            $out = $this->{'compile'.$token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the #block statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileBlock($expression)
    {
        $output = preg_replace_callback("/\n*\#block\s*\((.+?)(?:,(.+?))?\)\n*/m", function ($match) {
            array_shift($match);

            $content = null;

            if (count($match) == 2) {
                $content = $match[1];
            }

            if (is_null($content)) {
                return "<?php \$__tintin->stackManager->startStack({$match[0]}); ?>";
            } else {
                return "<?php \$__tintin->stackManager->startStack({$match[0]}, $content); ?>";
            }
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the #endblock statement
     *
     * @param $expression
     * @return string
     */
    protected function compileEndBlock($expression)
    {
        $output = preg_replace_callback("/\n*#endblock\n*/m", function () {
            return "<?php \$__tintin->stackManager->endStack(); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the #include statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $regex = "/\#include\s*\(((?:\n|\s|\t)*(?:.+?)(?:\n|\s|\t)*)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            return "<?php \$__tintin->stackManager->includeFile({$match[1]}, ['__tintin' => \$__tintin]); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the #extends statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        $regex = "/\#extends\s*\(((?:\n|\s|\t)*(?:.+?)(?:\n|\s|\t)*)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            return "<?php \$__tintin->stackManager->includeFIle({$match[1]}, ['__tintin' => \$__tintin]); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the #inject statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileInject($expression)
    {
        $regex = "/\#inject\s*\(((?:\n|\s|\t)*(?:.+?)(?:\n|\s|\t)*)\)/sm";

        $output = preg_replace_callback($regex, function ($match) {
            return "<?php echo \$__tintin->stackManager->getStack({$match[1]}); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
