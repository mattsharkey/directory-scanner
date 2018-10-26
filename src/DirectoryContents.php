<?php

namespace CreativeServices\Filesystem;

class DirectoryContents implements DirectoryContentsInterface, \OuterIterator
{
    private $iterator;

    private $path;

    public function __construct($path)
    {
        $this->path = $path;
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("Not a directory: $path");
        }
        $this->makeIterator();
    }

    /**
     * @return \SplFileInfo
     */
    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    /**
     * @return \OuterIterator
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function key()
    {
        return $this->makeRelativePath($this->getInnerIterator()->key());
    }

    public function next()
    {
        $this->getInnerIterator()->next();
    }

    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    /**
     * @return \OuterIterator
     */
    private function makeIterator()
    {
        $dir = new \RecursiveDirectoryIterator($this->path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $this->iterator = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::LEAVES_ONLY);
    }

    /**
     * @return string
     */
    private function makePathRoot()
    {
        return rtrim(realpath($this->path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $path
     * @return string
     */
    private function makeRelativePath($path)
    {
        $root = $this->makePathRoot();
        $path = realpath($path);
        if (substr($path, 0, strlen($root)) === $root) {
            return substr($path, strlen($root));
        } else {
            return $path;
        }
    }
}