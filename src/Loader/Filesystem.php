<?php
namespace Tintin;

use Tintin\Loader\LoaderInterace;

class Filesystem implements LoaderInterace
{
    /**
     * Dossier de base des fichiers des template.
     *
     * @var string
     */
    private $dirname;

    /**
     * Filesystem constructor.
     * @param $dirname
     */
    public function __construct($dirname)
    {
        $this->dirname = $dirname;
    }
}