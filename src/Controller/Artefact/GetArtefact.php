<?php
// src/Controller/Artefact/GetArtefact.php
namespace App\Controller\Artefact;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Artefact\ArtefactNormalizer;
use Symfony\Component\HttpFoundation\Request;

class GetArtefact extends ArtefactController
{
    #[Route(
        '/api/artefact/{id}',
        name: 'get_artefact_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getArtefactByID(int $id): Response
    {
        $artefact = $this->artefactRepository->findOneWithParams(array("id" => $id));
        return $this->response($artefact);
    }

    #[Route(
        '/api/artefact/{name}',
        name: 'get_artefact_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getArtefactByName(string $name, Request $request): Response
    {
        $show = null;
        if($request->query->get('show') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $artefact = $this->artefactRepository->findOneWithParams(array("name" => $name, "show" => $show));
        return $this->response($artefact);
    }

    private function response(mixed $artefact): Response
    {
        if($artefact){
            $serializer = new Serializer([new ArtefactNormalizer]);
            $data = $serializer->normalize([
                "artefact" => $artefact
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