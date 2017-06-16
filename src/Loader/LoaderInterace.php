<?php
namespace Tintin\Loader;

interface LoaderInterface
{
    /**
     * @return mixed
     */
    public function getCachePath();

    /**
     * @param string $filename
     * @return mixed
     */
    public function getFileContent($filename);

    /**
     * @param string $filename
     * @return mixed
     */
    public function isExpirate($filename);
}