<?php

namespace App\MessageHandler\Event;

use App\Message\Event\DeleteDogFileEvent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteDogFileEventHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __invoke(DeleteDogFileEvent $event)
    {
        if (!$event->getFilename()) {
            $this->logger->info("File " . $event->getFilename() . " not found");
            return;
        }

        echo "Delete from an event" . $event->getFilename();
    }
}
