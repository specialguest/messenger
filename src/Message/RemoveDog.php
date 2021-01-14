<?php

namespace App\Message;

class RemoveDog
{
    private int $dogId;

    public function __construct(int $dogId)
    {
        $this->dogId = $dogId;
    }

    public function getDogId(): int
    {
        return $this->dogId;
    }
}