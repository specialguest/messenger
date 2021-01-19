<?php

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteDogFile;
use App\Message\Command\RemoveDog;
use App\Message\Event\DeleteDogFileEvent;
use App\Repository\DogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RemoveDogHandler implements MessageHandlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private EntityManagerInterface $entityManager, private DogRepository $dogRepository, private MessageBusInterface $messageBus, private MessageBusInterface $eventBus)
    {
    }

    public function __invoke(RemoveDog $removeDog)
    {
        $dog = $this->dogRepository->find($removeDog->getDogId());

        if ($dog === null) {

            if ($this->logger) {
                $this->logger->alert(sprintf('Dog %d was missing', $removeDog->getDogId()));
            }

            return;
        }

        $this->entityManager->remove($dog);
        $this->entityManager->flush();

        // Appeler une seconde commande
        $this->messageBus->dispatch(new DeleteDogFile($dog->getFilename()));

        // Appeler un event
        $this->messageBus->dispatch(new DeleteDogFileEvent($dog->getFilename()));
    }
}
