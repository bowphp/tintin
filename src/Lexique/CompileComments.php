<?php

namespace Tintin\Lexique;

trait CompileComments
{
    /**
     * Compile the {# commentes #} statement
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComments($value)
    {
        $pattern = sprintf('/%s(.*?)%s/s', $this->comments[0], $this->comments[1]);
        
        return preg_replace($pattern, '', $value);
    }
}
