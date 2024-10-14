<?php
// src/Controller/Artefact/DeleteArtefact.php
namespace App\Controller\Artefact;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteArtefact extends ArtefactController
{
    #[Route(
        '/api/artefact/{id}',
        name: 'delete_artefact',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $artefact = $this->artefactRepository->findOneWithParams(array("id" => $id));

        if($artefact){
            $entityManager->remove($artefact);
            $entityManager->flush();

            return new Response("This artefact is deleted", 200, ['Content-Type', 'application/json']);
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