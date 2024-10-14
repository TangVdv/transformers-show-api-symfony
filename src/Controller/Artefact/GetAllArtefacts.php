<?php
// src/Controller/Artefact/GetAllArtefacts.php
namespace App\Controller\Artefact;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Normalizer\Artefact\ArtefactNormalizer;

class GetAllArtefacts extends ArtefactController
{
    #[Route(
        '/api/artefacts',
        name: 'get_artefacts',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $show = null;

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
        
        $artefacts = $this->artefactRepository->findAllWithParams($limit, $show);

        if($artefacts){
            $serializer = new Serializer([new ArtefactNormalizer]);
            $data = $serializer->normalize([
                "artefact_total" => count($artefacts),
                "limit" => $limit,
                "artefacts" => $artefacts
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