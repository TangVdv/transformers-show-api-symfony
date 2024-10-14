<?php
// src/Controller/Creator/GetCreator.php
namespace App\Controller\Creator;

use App\Normalizer\Creator\CreatorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;

class GetCreator extends CreatorController
{
    #[Route(
        '/api/creator/{id}',
        name: 'get_creator_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getCreatorByID(int $id): Response
    {
        $creator = $this->creatorRepository->findOneWithParams(array("id" => $id));
        return $this->response($creator);
    }

    #[Route(
        '/api/creator/{name}',
        name: 'get_creator_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getCreatorByName(string $name): Response
    {
        $creator = $this->creatorRepository->findOneWithParams(array("name" => $name));
        return $this->response($creator);
    }

    private function response(mixed $creator): Response
    {
        if($creator){
            $serializer = new Serializer([new CreatorNormalizer]);
            $data = $serializer->normalize([
                "creator" => $creator
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