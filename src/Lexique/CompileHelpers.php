<?php

namespace Tintin\Lexique;

use Tintin\Exception\BadDirectiveCalledException;

trait CompileHelpers
{
    /**
     * Compile the %if directive stack
     *
     * @param string $expression
     * @return string
     */
    protected function compileHelpersStack(string $expression): string
    {
        foreach (
            [
            "Auth", "Guest", "Lang", "Env", "Csrf", "Flash", "Production", 
            "HasFlash", "EndHelpers", "Empty", "NotEmpty", "Method", "Service"
            ] as $token
        ) {
            $out = $this->{"compile" . $token}($expression);
            if (strlen($out) !== 0) {
                $expression = $out;
            }
        }

        return $expression;
    }

    /**
     * Compile the %csrf directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileCsrf(string $expression): string
    {
        $output = preg_replace_callback('/\n*(%csrf)\n*/', function ($match) {
            array_shift($match);

            return "<?= csrf_field(); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %method token
     *
     * @param string $expression
     * @return string
     */
    protected function compileMethod(string $expression): string
    {
        $output = preg_replace_callback('/\n*(%method\s*\((.+?)\))\n*/', function ($match) {
            array_shift($match);

            $method = end($match);

            return "<?= method_field($method); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %service directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileService(string $expression): string
    {
        $output = preg_replace_callback('/\n*(%service\s*\((.+?)\))\n*/', function ($match) {
            array_shift($match);

            $definition = end($match);
            $parameters = preg_split("/\s*,\s*/", $definition);

            if (count($parameters) != 2) {
                throw new BadDirectiveCalledException();
            }

            [$variable, $service] = $parameters;
            $variable = trim($variable, '"\'');

            return "<?php \${$variable} = app($service); ?>";
        }, $expression);

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %auth directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileAuth(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%auth');
    }

    /**
     * Compile the %guest directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileGuest(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%guest');
    }

    /**
     * Compile the %lang directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileLang(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%lang');
    }

    /**
     * Compile the %flash directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileFlash(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%flash');
    }

    /**
     * Compile the %env directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEnv(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%env');
    }

    /**
     * Compile the %empty directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEmpty(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%empty');
    }

    /**
     * Compile the %notempty directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileNotEmpty(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%notempty');
    }

    /**
     * Compile the %production directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileProduction(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%production');
    }

    /**
     * Compile the %hasflash directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileHasFlash(string $expression): string
    {
        return $this->compileHelpersStatement($expression, '%hasflash');
    }

    /**
     * Compile the %endif directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileEndHelpers(string $expression): string
    {
        $output = preg_replace_callback(
            '/\n*(%endauth|%endguest|%endlang|%endenv|%endproduction|%endhasflash|%endempty|%endnotempty)\n*/',
            function ($match) {
                array_shift($match);

                return "<?php endif; ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }

    /**
     * Compile the %if directive
     *
     * Note: $o_lexic is the original PHP lexique
     *
     * @param string $expression
     * @param string $lexic
     *
     * @return string
     */
    private function compileHelpersStatement(string $expression, string $lexic): string
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

            if ($lexic == '%hasflash') {
                return "<?php if (session()->has(" . $params . ")): ?>";
            }

            if ($lexic == '%empty') {
                return "<?php if (empty(" . $params . ")): ?>";
            }

            if ($lexic == '%notempty') {
                return "<?php if (!empty(" . $params . ")): ?>";
            }

            if ($lexic == '%lang') {
                if (strlen(trim($params)) > 1) {
                    return "<?php if (client_locale() == " . $params . "): ?>";
                }
                $message = "The %lang take $1 parameter missing but $0 passed";
                return "<?php throw new \Tintin\Exception\BadDirectiveCalledException('$message') ?>";
            }

            if ($lexic == '%env') {
                if (strlen(trim($params)) > 1) {
                    return "<?php if (app_mode() == " . $params . "): ?>";
                }
                $message = "The %env take $1 parameter missing but $0 passed";
                return "<?php throw new \Tintin\Exception\BadDirectiveCalledException('$message') ?>";
            }

            if ($lexic == '%production') {
                if (strlen(trim($params)) == 0) {
                    return "<?php if (app_mode() == \"production\"): ?>";
                }
                $message = "The %production cannot take the parameters!";
                return "<?php throw new \Tintin\Exception\BadDirectiveCalledException('$message') ?>";
            }

            if ($lexic == '%flash') {
                if (strlen(trim($params)) == 0) {
                    $message = "The %flash take $1 parameter missing but $0 passed";
                    return "<?php throw new \Tintin\Exception\BadDirectiveCalledException('$message') ?>";
                }

                return "<?php echo session()->get(" . $params . "); ?>";
            }

            return $expression;
        }, $expression);

        return $output == $expression ? '' : $output;
    }
}
