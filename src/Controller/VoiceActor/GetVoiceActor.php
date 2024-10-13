<?php
// src/Controller/VoiceActor/GetVoiceActor.php
namespace App\Controller\VoiceActor;

use App\Normalizer\VoiceActor\VoiceActorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

class GetVoiceActor extends VoiceActorController
{
    #[Route(
        '/api/voiceactor/{id}',
        name: 'get_voiceactor_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getVoiceActorByID(int $id): Response
    {
        $voice_actor = $this->voiceactorRepository->findOneWithParams(array("id" => $id));
        return $this->response($voice_actor);
    }

    #[Route(
        '/api/voiceactor/{name}',
        name: 'get_voiceactor_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getVoiceActorByName(string $name): Response
    {
        $voice_actor = $this->voiceactorRepository->findOneWithParams(array("name" => $name));
        return $this->response($voice_actor);
    }

    private function response(mixed $voice_actor): Response
    {
        if($voice_actor){
            $serializer = new Serializer([new VoiceActorNormalizer]);
            $data = $serializer->normalize([
                "voiceactor" => $voice_actor
            ], "json");
            $json = $this->serializer->serialize($data, "json");
            return new Response($json, 200, ['Content-Type', 'application/json']);
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