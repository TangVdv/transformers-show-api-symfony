<?php
// src/Controller/Bot/GetAllBots.php
namespace App\Controller\Bot;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Bot\AllBotsNormalizer;
use Symfony\Component\HttpFoundation\Request;

class GetAllBots extends BotController
{
    #[Route(
        '/api/bots',
        name: 'get_bots',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $alt = null;
        $faction = null;

        if($request->query->get('limit') !== null && !empty($request->query->get('limit'))){
            if(filter_var($request->query->get('limit'), FILTER_VALIDATE_INT)){
                $limit = $request->query->getInt('limit');
            }
            else{
                return new Response("Parameter `limit` is in incorrect type format, `integer` is needed", 400, ['Content-Type', 'application/json']);
            }
        }

        if($request->query->get('alt') !== null && !empty($request->query->get('alt'))){
            $alt = filter_var($request->query->get('alt'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        if($request->query->get('faction') !== null && !empty($request->query->get('faction'))){
            $faction = filter_var($request->query->get('faction'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $bots = $this->botRepository->findAllWithParams($limit, $alt, $faction);

        if($bots){
            $serializer = new Serializer([new AllBotsNormalizer]);
            $data = $serializer->normalize([
                "bot_total" => count($bots),
                "limit" => $limit,
                "bots" => $bots
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