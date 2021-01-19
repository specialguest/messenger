<?php

namespace App\MessageHandler\Query;

use App\Message\Query\GetDogFileTotalCount;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetDogFileTotalCountHandler implements MessageHandlerInterface
{
    public function __invoke(GetDogFileTotalCount $getDogFileTotalCount)
    {
        // ...
        return 10;
    }
}
