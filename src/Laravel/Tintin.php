<?php

namespace Tintin\Laravel;

use Tintin\Tintin as BowTintin;

class Tintin extends BowTintin
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
