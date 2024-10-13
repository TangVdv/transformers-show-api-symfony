<?php
// src/Controller/VoiceActor/GetAllVoiceActors.php
namespace App\Controller\VoiceActor;

use App\Normalizer\VoiceActor\VoiceActorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class GetAllVoiceActors extends VoiceActorController
{
    #[Route(
        '/api/voiceactors',
        name: 'get_voiceactors',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $show = null;
        $bot = null;

        if($request->query->get('limit') !== null && !empty($request->query->get('limit'))){
            if(filter_var($request->query->get('limit'), FILTER_VALIDATE_INT)){
                $limit = $request->query->getInt('limit');
            }
            else{
                return new Response("Parameter `limit` is in incorrect type format, `integer` is needed", 400, ['Content-Type', 'application/json']);
            }
        }

        if($request->query->get('show') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        if($request->query->get('bot') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        
        $voice_actors = $this->voiceactorRepository->findAllWithParams($limit, $show, $bot);

        if($voice_actors){
            $serializer = new Serializer([new VoiceActorNormalizer]);
            $data = $serializer->normalize([
                "voiceactor_total" => count($voice_actors),
                "limit" => $limit,
                "voiceactors" => $voice_actors
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