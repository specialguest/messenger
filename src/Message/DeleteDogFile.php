<?php

namespace App\Message;

class DeleteDogFile
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function __toString()
    {
        return $this->filename;
    }
}
