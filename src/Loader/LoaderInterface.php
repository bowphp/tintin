<?php

namespace Tintin\Loader;

interface LoaderInterface
{
    /**
     * Get the cache repository path
     *
     * @return string
     */
    public function getCachePath(): string;

    /**
     * Get the absolute cache file path
     *
     * @param string $filename
     * @return string
     */
    public function getCacheFileResolvedPath(string $filename): string;

    /**
     * Get file content
     *
     * @param string $filename
     * @return mixed
     */
    public function getFileContent(string $filename);

    /**
     * Check if file exists
     *
     * @param string $filename
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * Check if file is expire
     *
     * @param string $filename
     * @return bool
     */
    public function isExpired(string $filename): bool;

    /**
     * Check if file is cached
     *
     * @param string $filename
     * @return bool
     */
    public function isCached(string $filename): bool;

    /**
     * Make cache
     *
     * @param string $filename
     * @param string $data
     * @return bool
     */
    public function cache(string $filename, string $data): bool;

    /**
     * Throw load error
     *
     * @param string $message
     * @return void
     * @throws \Exception
     */
    public function failLoading(string $message);
}
