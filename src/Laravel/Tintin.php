<?php

namespace Tintin\Laravel;

class Tintin extends \Tintin\Tintin
{
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
