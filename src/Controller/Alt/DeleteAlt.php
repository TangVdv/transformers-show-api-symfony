<?php
// src/Controller/Alt/DeleteAlt.php
namespace App\Controller\Alt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteAlt extends AltController
{
    #[Route(
        '/api/alt/{id}',
        name: 'delete_alt',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $alt = $this->altRepository->findOneWithParams(array("id" => $id));

        if($alt){
            $entityManager->remove($alt);
            $entityManager->flush();

            return new Response("This alt is deleted", 200, ['Content-Type', 'application/json']);
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