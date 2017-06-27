<?php
namespace Tintin\Loader;

interface LoaderInterface
{
    /**
     * @return mixed
     */
    public function getCachePath();

    /**
     * @param $filebane
     * @return mixed
     */
    public function getCacheFileResolvedPath($filebane);

    /**
     * @param string $filename
     * @return mixed
     */
    public function getFileContent($filename);

    /**
     * @param $filename
     * @return bool
     */
    public function fileExists($filename);

    /**
     * @param string $filename
     * @return mixed
     */
    public function isExpirated($filename);

    /**
     * @param string $filename
     * @return mixed
     */
    public function isCached($filename);

    /**
     * @param string $filename
     * @param string $data
     * @return mixed
     */
    public function cache($filename, $data);

    /**
     * @param string $message
     * @throws \Exception
     */
    public function failLoading($message);
}