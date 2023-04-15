<?php

namespace Tintin\Lexique;

trait CompileMacro
{
    /**
     * Complie the %macro and %endmacro statements
     *
     * @param string $expression
     * @return string
     */
    public function compileMacro(string $expression): string
    {
        $regex = "/%macro(.+?)%endmacro/";

        return 'Hello {{ $name }}';
    }
}