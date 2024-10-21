<?php
// src/Controller/ConceptArt/GetConceptArt.php
namespace App\Controller\ConceptArt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\ConceptArt\ConceptArtNormalizer;
use Symfony\Component\HttpFoundation\Request;

class GetConceptArt extends ConceptArtController
{
    #[Route(
        '/api/conceptart/{id}',
        name: 'get_concept_art_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getConceptArtByID(int $id): Response
    {
        $conceptart = $this->conceptArtRepository->findOneWithParams(array("id" => $id));
        return $this->response($conceptart);
    }

    #[Route(
        '/api/conceptart/{title}',
        name: 'get_concept_art_name',
        methods: ['GET'],
        requirements: ['title' => '\w+']
    )]
    public function getConceptArtByName(string $title, Request $request): Response
    {
        $show = null;
        if($request->query->get('show') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $entity = null;
        if($request->query->get('entity') !== null && !empty($request->query->get('entity'))){
            $entity = filter_var($request->query->get('entity'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $artist = null;
        if($request->query->get('artist') !== null && !empty($request->query->get('artist'))){
            $artist = filter_var($request->query->get('artist'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $conceptart = $this->conceptArtRepository->findOneWithParams(array("title" => $title, "show" => $show, "entity" => $entity, "artist" => $artist));
        return $this->response($conceptart);
    }

    private function response(mixed $conceptart): Response
    {
        if($conceptart){
            $serializer = new Serializer([new ConceptArtNormalizer]);
            $data = $serializer->normalize([
                "concept_art" => $conceptart
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