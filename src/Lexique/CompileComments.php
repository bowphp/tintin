<?php

namespace Tintin\Lexique;

trait CompileComments
{
    /**
     * Compile the {# comments #} statement
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComments(string $value): string
    {
        $pattern = sprintf('/%s(.*?)%s/', $this->comments[0], $this->comments[1]);

        return preg_replace($pattern, '', $value);
    }
}
