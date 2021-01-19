<?php

namespace App\MessageHandler\Command;

use App\Message\Command\AddDog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

# On peut utiliser MessageSubscriberInterface au lieu de MessageHandlerInterface
class AddDogHandler implements MessageSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(AddDog $addDog)
    {
    }

    public static function getHandledMessages(): iterable
    {
        yield AddDog::class => [
            'method' => '__invoke',
            'priority' => 10, // but Messenger is still FIFO, only useful when you have 2+ Message
            //'from_transport' => 'async'  // Called nly when the message is consume by 'async' transport
        ];
    }
}
