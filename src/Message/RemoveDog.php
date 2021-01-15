<?php

namespace App\Message;

class RemoveDog
{
    public function __construct(private int $dogId)
    {
    }

    public function getDogId(): int
    {
        return $this->dogId;
    }
}
