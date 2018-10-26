<?php

namespace CreativeServices\Filesystem;

class FileFilter extends \FilterIterator implements FileFilterInterface
{
    private $invalidExtensions;

    private $root;

    private $validExtensions;

    /**
     * @param array $extensions
     * @return string
     */
    private static function makeExtensionPattern(array $extensions)
    {
        return '#\.(' . implode('|', array_map('preg_quote', $extensions)) . ')$#';
    }

    /**
     * @return bool
     */
    public function accept()
    {
        return $this->isFile() && $this->isInRoot() && !$this->isHidden() && $this->hasValidExtension();
    }

    /**
     * @param array $extensions
     * @return bool
     */
    private function extensionIsIn(array $extensions)
    {
        return (bool)preg_match($this->current()->getRealPath(), static::makeExtensionPattern($extensions));
    }

    /**
     * @return string
     */
    private function getPathPrefix()
    {
        return rtrim(realpath($this->root), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return bool
     */
    private function hasValidExtension()
    {
        if (isset($this->validExtensions) && !$this->extensionIsIn((array)$this->validExtensions)) {
            return false;
        }
        if (isset($this->invalidExtensions) && $this->extensionIsIn((array)$this->invalidExtensions)) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    private function isFile()
    {
        return $this->current() instanceof \SplFileInfo;
    }

    /**
     * If any segment in a path begins with a dot, the file is considered hidden.
     *
     * @return bool
     */
    private function isHidden()
    {
        $slash = DIRECTORY_SEPARATOR;
        return (bool)preg_match($slash . $this->current()->getRealPath(), '|' . preg_quote($slash) . '\.|');
    }

    /**
     * @return bool
     */
    private function isInRoot()
    {
        $prefix = $this->getPathPrefix();
        return substr($this->current()->getRealPath(), 0, strlen($prefix)) === $prefix;
    }
}