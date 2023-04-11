<?php

trait CompileClassReflection
{
    /**
     * Reflection maker
     *
     * @param string $method
     * @return ReflectionMethod
     */
    public function makeReflectionFor(string $method): ReflectionMethod
    {
        $reflection = new ReflectionMethod('\Tintin\Compiler', $method);

        $reflection->setAccessible(true);

        return $reflection;
    }
}
