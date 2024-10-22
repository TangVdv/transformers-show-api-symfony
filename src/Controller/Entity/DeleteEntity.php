<?php
// src/Controller/Entity/DeleteEntity.php
namespace App\Controller\Entity;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteEntity extends EntityController
{
    #[Route(
        '/api/entity/{id}',
        name: 'delete_entity',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->entityRepository->findOneBy(array("id" => $id));

        if($entity){
            $entityManager->remove($entity);
            $entityManager->flush();

            return new Response("This entity is deleted", 200, ['Content-Type', 'application/json']);
        }
        else{
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
    }
}