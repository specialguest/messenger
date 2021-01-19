<?php

namespace App\Message\Command;

class DeleteDogFile implements \Stringable
{
    public function __construct(private string $filename)
    {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function __toString(): string
    {
        return $this->filename;
    }
}
