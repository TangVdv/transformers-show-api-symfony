<?php
// src/Controller/Actor/GetActor.php
namespace App\Controller\Actor;

use App\Normalizer\Actor\ActorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class GetActor extends ActorController
{
    #[Route(
        '/api/actor/{id}',
        name: 'get_actor_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getActorByID(int $id): Response
    {
        $actor = $this->actorRepository->findOneWithParams(array("id" => $id));
        return $this->response($actor);
    }

    #[Route(
        '/api/actor/{name}',
        name: 'get_actor_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getActorByName(string $name, Request $request): Response
    {
        $actor = $this->actorRepository->findOneWithParams(array("name" => $name));
        return $this->response($actor);
    }

    private function response(mixed $actor): Response
    {
        if($actor){
            $serializer = new Serializer([new ActorNormalizer]);
            $data = $serializer->normalize([
                "actor" => $actor
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