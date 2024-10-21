<?php
// src/Controller/ConceptArt/DeleteConceptArt.php
namespace App\Controller\ConceptArt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteConceptArt extends ConceptArtController
{
    #[Route(
        '/api/conceptart/{id}',
        name: 'delete_concept_art',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $conceptart = $this->conceptArtRepository->findOneWithParams(array("id" => $id));

        if($conceptart){
            $entityManager->remove($conceptart);
            $entityManager->flush();

            return new Response("This concept art is deleted", 200, ['Content-Type', 'application/json']);
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