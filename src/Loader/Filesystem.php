<?php

namespace Tintin\Loader;

class Filesystem implements LoaderInterface
{
    /**
     * config
     *
     * @var array
     */
    private $config;

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
     * @inheritdoc
     */
    public function getFileResolvedPath($filename)
    {
        $filename = str_replace('.', '/', $filename);

        return $this->config['path'].'/'.$filename.'.'.$this->getExtension();
    }

    /**
     * @inheritdoc
     */
    public function getCacheFileResolvedPath($filename)
    {
        if (!$this->exists($filename)) {
            $this->failLoading($filename . ' file not exists !');
        }

        $md5 = sha1($filename);

        $dirname = substr($md5, 0, 2);

        return $this->getCachePath().'/'.$dirname.'/'.$md5.'.php';
    }

    /**
     * @inheritdoc
     */
    public function getCachePath()
    {
        return isset($this->config['cache']) ? $this->config['cache'] : sys_get_temp_dir();
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return isset($this->config['extension']) ? trim($this->config['extension'], '.') : 'tintin.php';
    }

    /**
     * @inheritdoc
     */
    public function getFileContent($filename)
    {
        return file_get_contents($this->getFileResolvedPath($filename));
    }

    /**
     * @inheritdoc
     */
    public function isExpirated($filename)
    {
        if (!$this->isCached($filename)) {
            return true;
        }

        $fileatime = fileatime(
            $this->getFileResolvedPath($filename)
        );

        $cache_filetime = fileatime(
            $this->getCacheFileResolvedPath($filename)
        );

        return $fileatime > $cache_filetime;
    }

    /**
     * @inheritdoc
     */
    public function isCached($filename)
    {
        return file_exists(
            $this->getCacheFileResolvedPath($filename)
        );
    }

    /**
     * @inheritdoc
     */
    public function exists($filename)
    {
        return file_exists(
            $this->getFileResolvedPath($filename)
        );
    }

    /**
     * @inheritdoc
     */
    public function cache($filename, $config)
    {
        $md5 = sha1($filename);

        $dirname = substr($md5, 0, 2);

        if (! is_dir($this->getCachePath().'/'.$dirname)) {
            mkdir($this->getCachePath().'/'.$dirname);
        }

        $path = $this->getCachePath().'/'.$dirname.'/'.$md5.'.php';

        return file_put_contents($path, $config);
    }

    /**
     * @inheritdoc
     */
    public function failLoading($message)
    {
        throw new \Tintin\Exception\FileNotFoundException($message);
    }
}
