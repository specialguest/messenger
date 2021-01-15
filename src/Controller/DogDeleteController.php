<?php

namespace App\Controller;

use App\Entity\Dog;
use App\Message\RemoveDog;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

//https://github.com/zircote/swagger-php/blob/master/Examples/petstore-3.0/controllers/Pet.php
class DogDeleteController extends AbstractController
{
    /**
     *
     * Delete a dog.
     *
     * @ParamConverter("dog", options={"mapping": {"dogId"   : "id"}})
     * @OA\Delete(
     *     path="api/dogs/{dogId}",
     *     tags={"dogs"},
     *     summary="Deletes a dog",
     *     operationId="deleteDog",
     *     @OA\Parameter(
     *         name="dogId",
     *         in="path",
     *         description="Dog id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Dog deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dog not found",
     *     )
     * )
     */
    #[\Symfony\Component\Routing\Annotation\Route(path: '/api/dogs/{dogId}', methods: ['DELETE'])]
    public function delete(Dog $dog, MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch( new RemoveDog($dog->getId()));
        return $this->json(null, 204);
    }
}
