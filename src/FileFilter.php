<?php

namespace CreativeServices\Filesystem;

class FileFilter extends \FilterIterator implements FileFilterInterface
{
    private $invalidExtensions;

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
        return $this->isFile() && !$this->isHidden() && $this->hasValidExtension();
    }

    public function allowExtensions(array $extensions)
    {
        $this->validExtensions = $extensions;
    }

    public function rejectExtensions(array $extensions)
    {
        $this->invalidExtensions = $extensions;
    }

    /**
     * @param array $extensions
     * @return bool
     */
    private function extensionIsIn(array $extensions)
    {
        return (bool)preg_match(static::makeExtensionPattern($extensions), $this->current()->getPathname());
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
        $quotedSlash = preg_quote($slash);
        $pattern = "|{$quotedSlash}\.[^\.{$quotedSlash}]|";
        return (bool)preg_match($pattern, $slash . $this->current()->getPathname());
    }
}