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
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function getFileResolvedPath($file)
    {
        return $this->data['cache'].'/'.ltrim('/', $file);
    }

    /**
     * @return mixed
     */
    public function getCachePath()
    {
        return $this->data['cache'];
    }

    /**
     * @return mixed
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
        return $this->data['extension'];
    }

    /**
     * @inheritdoc
     */
    public function isExpirate($filename)
    {
        return $this->data['extension'];
    }
}