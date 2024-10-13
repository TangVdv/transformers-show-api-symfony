<?php
// src/Controller/Alt/GetAllAlts.php
namespace App\Controller\Alt;

use App\Normalizer\Alt\AltNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class GetAllAlts extends AltController
{
    #[Route(
        '/api/alts',
        name: 'get_alts',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $bot = null;

        if($request->query->get('limit') !== null && !empty($request->query->get('limit'))){
            if(filter_var($request->query->get('limit'), FILTER_VALIDATE_INT)){
                $limit = $request->query->getInt('limit');
            }
            else{
                return new Response("Parameter `limit` is in incorrect type format, `integer` is needed", 400, ['Content-Type', 'application/json']);
            }
        }

        if($request->query->get('bot') !== null && !empty($request->query->get('bot'))){
            $bot = filter_var($request->query->get('bot'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        
        $alts = $this->altRepository->findAllWithParams($limit, $bot);

        if($alts){
            $serializer = new Serializer([new AltNormalizer]);
            $data = $serializer->normalize([
                "alt_total" => count($alts),
                "limit" => $limit,
                "alts" => $alts
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