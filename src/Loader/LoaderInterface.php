<?php

namespace Tintin\Loader;

interface LoaderInterface
{
    /**
     * Get the cache reporitory path
     *
     * @return mixed
     */
    public function getCachePath();

    /**
     * Get the absolute cache file path
     *
     * @param $filebane
     * @return mixed
     */
    public function getCacheFileResolvedPath($filebane);

    /**
     * Get file content
     *
     * @param string $filename
     * @return mixed
     */
    public function getFileContent($filename);

    /**
     * Check if file exists
     *
     * @param $filename
     * @return bool
     */
    public function fileExists($filename);

    /**
     * Check if file is expirate
     *
     * @param string $filename
     * @return mixed
     */
    public function isExpirated($filename);

    /**
     * Check if file is cached
     *
     * @param string $filename
     * @return mixed
     */
    public function isCached($filename);

    /**
     * Make cache
     *
     * @param string $filename
     * @param string $data
     * @return mixed
     */
    public function cache($filename, $data);

    /**
     * Throw load error
     *
     * @param string $message
     * @throws \Exception
     */
    public function failLoading($message);
}
