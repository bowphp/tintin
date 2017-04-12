<?php
namespace Tintin\Lexique;

trait CompileExtends
{
    /**
     * @param $expression
     * @return string
     */
    protected function compileBlock($expression)
    {
        return '';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        return '';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        return '';
    }
}