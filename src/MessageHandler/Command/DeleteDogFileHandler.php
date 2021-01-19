<?php

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteDogFile;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteDogFileHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(DeleteDogFile $deleteDogFile)
    {
        if (!$deleteDogFile) {
            $this->logger->info("File ${deleteDogFile} not found");
            return;
        }

        echo "delete ${deleteDogFile} !";
    }
}
