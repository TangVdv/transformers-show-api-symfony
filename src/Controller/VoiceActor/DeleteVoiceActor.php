<?php
// src/Controller/VoiceActor/DeleteVoiceActor.php
namespace App\Controller\VoiceActor;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteVoiceActor extends VoiceActorController
{
    #[Route(
        '/api/voiceactor/{id}',
        name: 'delete_voiceactor',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $voice_actor = $this->voiceactorRepository->findOneWithParams(array("id" => $id));

        if($voice_actor){
            $entityManager->remove($voice_actor);
            $entityManager->flush();

            return new Response("This voice actor is deleted", 200, ['Content-Type', 'application/json']);
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