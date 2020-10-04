<?php

namespace Tintin\Laravel;

use Tintin\Loader\LoaderInterface;

class Tintin extends \Tintin\Tintin
{
    /**
     * Add namespace
     *
     * @param string $namespaces
     * @param string $app_path
     * @return mixed
     */
    public function addNamespace($namespaces, $app_path)
    {
        //
    }

    /**
     * Alias of render method
     *
     * @param string $filename
     * @param array $params
     * @return mixed
     */
    public function make($filename, array $params = [])
    {
        return parent::render($filename, $params);
    }
}
