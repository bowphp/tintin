<?php

namespace Tintin\Lexique;

trait CompileClass
{
    /**
     * Compile the %class directive
     *
     * @param string $expression
     * @return string
     */
    protected function compileClass(string $expression): string
    {
        $output = preg_replace_callback(
            '/^%class\s*\((.*)\)$/',
            function ($match) {
                array_shift($match);

                $string = str_replace("'", '', $match[0]);

                $classes = explode(', ', trim($string, "[]"));

                $enableClasses = array_filter(array_map(function ($value) {
                    $parts = explode('=>', $value);

                    $class = trim($parts[0]);

                    if (!isset($parts[1])) {
                        return $class;
                    }

                    if (filter_var(trim($parts[1]), FILTER_VALIDATE_BOOLEAN)) {
                        return $class;
                    }
                }, $classes), function ($value) {
                    return !is_null($value);
                });

                $classes = implode(' ', $enableClasses);

                return "<?= 'class=\"$classes\"' ?>";
            },
            $expression
        );

        return $output == $expression ? '' : $output;
    }
}
