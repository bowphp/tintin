<?php

namespace Tintin\Lexique;

trait CompileHelpers
{
    /**
     * Compile the if statement stack
     *
     * @param string $expression
     * @return string
     */
    protected function compileHelpersStack(string $expression): string
    {
        foreach (['Auth', 'Guest', 'Lang', 'Env', 'EndHelpers', "Production"] as $token) {
            $out = $this->{'compile' . $token}($expression);

            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %auth statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileAuth(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%auth');
    }

    /**
     * Compile the %guest statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileGuest(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%guest');
    }

    /**
     * Compile the %lang statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileLang(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%lang');
    }

    /**
     * Compile the %env statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEnv(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%env');
    }

    /**
     * Compile the %production statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileProduction(string $expression): string
    {
        return $this->compileAuthStatement($expression, '%production');
    }

    /**
     * Compile the %endif statement
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndHelpers(string $expression): string
    {
        $output = preg_replace_callback(
            '/\n*(%endauth|%endguest|%endlang|%endenv|%endproduction)\n*/',
            function ($match) {
                array_shift($match);

                return "<?php endif; ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %if statement
     *
     * Note: $o_lexic is the original PHP lexique
     *
     * @param string $expression
     * @param string $lexic
     *
     * @return string
     */
    private function compileAuthStatement(string $expression, string $lexic): string
    {
        $regex = sprintf($this->option_condition_pattern, $lexic);

        $output = preg_replace_callback($regex, function ($match) use ($lexic, $expression) {
            array_shift($match);

            $params = count($match) > 2 ? end($match) : '';

            if ($lexic == '%auth') {
                return "<?php if (auth(" . $params . ")->check()): ?>";
            }

            if ($lexic == '%guest') {
                return "<?php if (!auth(" . $params . ")->check()): ?>";
            }

            if ($lexic == '%lang') {
                return "<?php if (client_locale() == " . $params . "): ?>";
            }

            if ($lexic == '%env') {
                return "<?php if (app_mode() == " . $params . "): ?>";
            }

            if ($lexic == '%production') {
                if (strlen(trim($params)) > 0) {
                    return '<?php throw new \ErrorException("The %production cannot take the parameters!") ?>';
                }
                return "<?php if (app_mode() == \"production\"): ?>";
            }

            return $expression;
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
