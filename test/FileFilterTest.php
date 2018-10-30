<?php

namespace CreativeServices\Filesystem;

use PHPUnit\Framework\TestCase;

class FileFilterTest extends TestCase
{
    public function testUnfiltered()
    {
        $files = ['file.twig' => new \SplFileInfo('/path/to/file.twig')];
        $iterator = new \ArrayIterator($files);

        $filter = new FileFilter($iterator);

        $filtered = iterator_to_array($filter);
        $this->assertEquals($files, $filtered);
    }

    public function testHidden()
    {
        $files = ['file.twig' => new \SplFileInfo('/path/to/.hidden-file.twig')];
        $iterator = new \ArrayIterator($files);

        $filter = new FileFilter($iterator);

        $filtered = iterator_to_array($filter);
        $this->assertCount(0, $filtered);
    }

    public function testDotDirectoriesAreNotHidden()
    {
        $files = ['file.twig' => new \SplFileInfo('/path/to/../to/file.twig')];
        $iterator = new \ArrayIterator($files);

        $filter = new FileFilter($iterator);

        $filtered = iterator_to_array($filter);
        $this->assertCount(1, $filtered);
    }

    public function testExtensionAllowed()
    {
        $file = new \SplFileInfo('/path/to/file.twig');
        $iterator = new \ArrayIterator([$file]);

        $filter = new FileFilter($iterator);
        $filter->allowExtensions(['twig']);

        $arr = iterator_to_array($filter);
        $this->assertCount(1, $arr);
    }

    public function testExtensionNotAllowed()
    {
        $file = new \SplFileInfo('/path/to/file.twig');
        $iterator = new \ArrayIterator([$file]);

        $filter = new FileFilter($iterator);
        $filter->allowExtensions(['txt']);

        $arr = iterator_to_array($filter);
        $this->assertCount(0, $arr);
    }

    public function testExtensionRejected()
    {
        $file = new \SplFileInfo('/path/to/file.twig');
        $iterator = new \ArrayIterator([$file]);

        $filter = new FileFilter($iterator);
        $filter->rejectExtensions(['twig']);

        $arr = iterator_to_array($filter);
        $this->assertCount(0, $arr);
    }

    public function testExtensionNotRejected()
    {
        $file = new \SplFileInfo('/path/to/file.twig');
        $iterator = new \ArrayIterator([$file]);

        $filter = new FileFilter($iterator);
        $filter->rejectExtensions(['txt']);

        $arr = iterator_to_array($filter);
        $this->assertCount(1, $arr);
    }
}