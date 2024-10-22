<?php
// src/Controller/VoiceLine/GetAllVoiceLines.php
namespace App\Controller\VoiceLine;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Normalizer\VoiceLine\VoiceLineNormalizer;

class GetAllVoiceLines extends VoiceLineController
{
    #[Route(
        '/api/voicelines',
        name: 'get_voice_lines',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $show = null;
        $entity = null;

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
        
        if($request->query->get('entity') !== null && !empty($request->query->get('entity'))){
            $entity = filter_var($request->query->get('entity'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        
        $voicelines = $this->voiceLineRepository->findAllWithParams($limit, $show, $entity);

        if($voicelines){
            $serializer = new Serializer([new VoiceLineNormalizer]);
            $data = $serializer->normalize([
                "voice_line_total" => count($voicelines),
                "limit" => $limit,
                "voice_lines" => $voicelines
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