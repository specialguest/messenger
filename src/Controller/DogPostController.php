<?php

namespace App\Controller;

use App\Entity\Dog;
use App\Message\Command\AddDog;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Validator\Validator\ValidatorInterface;

//https://github.com/zircote/swagger-php/blob/master/Examples/petstore-3.0/controllers/Pet.php
class DogPostController extends AbstractController
{
    /**
     * Add a new dog.
     *
     * @OA\Post(
     *     path="/api/dogs",
     *     tags={"dogs"},
     *     summary="Add a dog",
     *     operationId="addDog",
     *     @OA\Response(
     *         response=201,
     *         description="Dog created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name of the dog",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="filename",
     *                     description="Filename of the dog's image",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="birthdate",
     *                     description="Birthdate of the dog",
     *                     type="string",
     *                     format="date-time"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/api/dogs', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator, MessageBusInterface $messageBus, EntityManagerInterface  $entityManager): JsonResponse
    {
        $data = $request->request->all();
        $dog = new Dog();
        if (array_key_exists('name', $data)) {
            $dog->setName($data['name']);
        }
        if (array_key_exists('filename', $data)) {
            $dog->setFilename($data['filename']);
        }
        $errors = $validator->validate($dog);
        if (count($errors) > 0) {
            return $this->json('Error', 400);
        }
        $entityManager->persist($dog);
        $entityManager->flush();
        $message = new AddDog($dog->getId());
        $envelope = new Envelope($message, [
            new DelayStamp(3000),  // Will create a delay queue in RabbitMQ
            // new AmqpStamp('normal') // Force normal queue
        ]);
        $messageBus->dispatch($envelope);

        return $this->json('Dog added!', 201);
    }
}
