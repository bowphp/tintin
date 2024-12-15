<?php

namespace Tintin\Lexique;

trait CompileComments
{
    /**
     * Compile the {## comments ##} directive
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComments(string $value): string
    {
        $pattern = '/\{\#\#.*?\#\#\}/s';

        return preg_replace($pattern, '', $value);
    }
}
