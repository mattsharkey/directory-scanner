<?php

namespace CreativeServices\DirectoryScanner;

class DirectoryScanner implements DirectoryScannerInterface, \OuterIterator
{
    private $iterator;

    private $path;

    private $pattern;

    public function __construct($path, $pattern = null)
    {
        $this->path = $path;
        if (isset($pattern)) {
            $this->pattern = $pattern;
        }
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("Not a directory: $path");
        }
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
        if (!isset($this->iterator)) {
            $this->iterator = $this->makeIterator();
        }
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
        $files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::LEAVES_ONLY);
        if (isset($this->pattern)) {
            return new \RegexIterator($files, $this->pattern);
        } else {
            return $files;
        }
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
        if (substr($path, 0, strlen($root)) !== $root) {
            throw new \InvalidArgumentException("Path not in directory: $path");
        }
        return substr($path, strlen($root));
    }
}