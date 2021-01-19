<?php

namespace App\Controller;

use App\Message\Query\GetDogFileTotalCount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function homepage(MessageBusInterface $queryBus)
    {
        $envelope = $queryBus->dispatch(new GetDogFileTotalCount());
        /** @var HandledStamp $handeld */
        $handeld = $envelope->last(HandledStamp::class);

        $fileCount = $handeld->getResult();
        return $this->render('main/homepage.html.twig', ['fileCount' => $fileCount]);
    }
}
