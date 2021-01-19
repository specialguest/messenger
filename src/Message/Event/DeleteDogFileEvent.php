<?php

namespace App\Message\Event;

class DeleteDogFileEvent
{
    public function __construct(private string $filename)
    {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
