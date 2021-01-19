<?php

namespace App\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;

class AuditMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(UniqueIdStamp::class)) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);
        $context = [
            'id' => $stamp->getUniqueId(),
            'class' => $envelope->getMessage()::class
        ];

        if ($envelope->last(ReceivedStamp::class)) {
            $this->logger->info('[{id}] Received & handling {class}', $context);
        } elseif ($envelope->last(SentStamp::class)) {
            $this->logger->info('[{id}] sent {class}', $context);
        } else {
            $this->logger->info('[{id}] Handling or sending {class}', $context);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
