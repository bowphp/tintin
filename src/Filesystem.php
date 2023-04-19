<?php

namespace Tintin;

use Tintin\Exception\FileNotFoundException;

class Filesystem implements LoaderInterface
{
    /**
     * config
     *
     * @var array
     */
    private array $config;

    /**
     * Filesystem constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        if (isset($this->config['cache'])) {
            $this->config['cache'] = rtrim($this->config['cache'], '/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileResolvedPath(string $filename): string|bool
    {
        $filename = str_replace('.', '/', $filename);
        $paths = (array) $this->config['path'];

        foreach ($paths as $path) {
            $full_filename = $path  . '/' . $filename . '.' . $this->getExtension();
            $realpath = realpath($full_filename);
            if ($realpath !== false && file_exists($realpath)) {
                return $realpath;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheFileResolvedPath(string $filename): string
    {
        if (!$this->exists($filename)) {
            $this->failLoading($filename . ' file not exists !');
        }

        $md5 = sha1($filename);

        $dirname = substr($md5, 0, 2);

        return realpath($this->getCachePath() . '/' . $dirname . '/' . $md5 . '.php');
    }

    /**
     * {@inheritdoc}
     */
    public function getCachePath(): string
    {
        return $this->config['cache'] ?? sys_get_temp_dir();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtension(): string
    {
        return $this->config['extension'] ?? 'tintin.php';
    }

    /**
     * {@inheritdoc}
     */
    public function getFileContent(string $filename): string
    {
        return file_get_contents($this->getFileResolvedPath($filename));
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired(string $filename): bool
    {
        if (!$this->isCached($filename)) {
            return true;
        }

        $fileatime = filemtime(
            $this->getFileResolvedPath($filename)
        );

        $cache_fileatime = fileatime(
            $this->getCacheFileResolvedPath($filename)
        );

        return $fileatime > $cache_fileatime;
    }

    /**
     * {@inheritdoc}
     */
    public function isCached(string $filename): bool
    {
        return file_exists(
            $this->getCacheFileResolvedPath($filename)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $filename): bool
    {
        return file_exists(
            $this->getFileResolvedPath($filename)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function cache(string $filename, $config): bool
    {
        $md5 = sha1($filename);

        $dirname = substr($md5, 0, 2);

        if (! is_dir($this->getCachePath() . '/' . $dirname)) {
            mkdir($this->getCachePath() . '/' . $dirname);
        }

        $path = $this->getCachePath() . '/' . $dirname . '/' . $md5 . '.php';

        return (bool) file_put_contents($path, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function failLoading(string $message)
    {
        throw new \Tintin\Exception\FileNotFoundException($message);
    }
}
