<?php
namespace Tintin\Loader;

class Filesystem implements LoaderInterface
{
    /**
     * Dossier de base des fichiers des template.
     *
     * @var array
     */
    private $data;

    /**
     * Filesystem constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        if (isset($this->data['cache'])) {
            $this->data['cache'] = rtrim($this->data['cache'], '/');
        }
    }

    /**
     * @inheritdoc
     */
    public function getFileResolvedPath($filename)
    {
        return $this->data['path'].'/'.$filename.'.'.$this->getExtension();
    }

    /**
     * @inheritdoc
     */
    public function getCacheFileResolvedPath($filename)
    {
        $md5 = sha1($filename);
        $dirname = substr($md5, 0, 2);
        return $this->getCachePath().'/'.$dirname.'/'.$md5.'.php';
    }

    /**
     * @inheritdoc
     */
    public function getCachePath()
    {
        return isset($this->data['cache']) ? $this->data['cache'] : sys_get_temp_dir();
    }

    /**
     * @inheritdoc
     */
    public function getExtension()
    {
        return isset($this->data['extension']) ? $this->data['extension'] : 'tintin.php';
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
        if ($this->isCached($filename)) {
            return fileatime($this->getFileResolvedPath($filename)) > fileatime($this->getCacheFileResolvedPath($filename));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isCached($filename)
    {
        return file_exists($this->getCacheFileResolvedPath($filename));
    }

    /**
     * @inheritdoc
     */
    public function fileExists($filename)
    {
        return file_exists($this->getFileResolvedPath($filename));
    }

    /**
     * @inheritdoc
     */
    public function cache($filename, $data)
    {
        $md5 = sha1($filename);
        $dirname = substr($md5, 0, 2);

        if (! is_dir($this->getCachePath().'/'.$dirname)) {
            mkdir($this->getCachePath().'/'.$dirname);
        }

        file_put_contents($this->getCachePath().'/'.$dirname.'/'.$md5.'.php', $data);
    }

    /**
     * @inheritdoc
     */
    public function failLoading($message)
    {
        throw new \Exception($message);
    }
}