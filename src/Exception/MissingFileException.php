<?php

namespace CreativeServices\Filesystem\Exception;

class MissingFileException extends \DomainException
{
    private $path;
}