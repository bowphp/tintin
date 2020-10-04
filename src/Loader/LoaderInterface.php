<?php

namespace Tintin\Loader;

interface LoaderInterface
{
    /**
     * Get the cache repository path
     *
     * @return mixed
     */
    public function getCachePath();

    /**
     * Get the absolute cache file path
     *
     * @param string $filename
     *
     * @return mixed
     */
    public function getCacheFileResolvedPath($filename);

    /**
     * Get file content
     *
     * @param string $filename
     *
     * @return mixed
     */
    public function getFileContent($filename);

    /**
     * Check if file exists
     *
     * @param string $filename
     *
     * @return bool
     */
    public function exists($filename);

    /**
     * Check if file is expire
     *
     * @param string $filename
     * @return mixed
     */
    public function isExpired($filename);

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
     * @return void
     * 
     * @throws \Exception
     */
    public function failLoading($message);
}
