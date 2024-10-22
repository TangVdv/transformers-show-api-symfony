<?php
// src/Controller/Entity/GetAllEntities.php
namespace App\Controller\Entity;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Normalizer\Entity\EntityNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetAllEntities extends EntityController
{
    #[Route(
        '/api/entities',
        name: 'get_entities',
        methods: ['GET']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request): Response
    {
        $limit = 10;

        if($request->query->get('limit') !== null && !empty($request->query->get('limit'))){
            if(filter_var($request->query->get('limit'), FILTER_VALIDATE_INT)){
                $limit = $request->query->getInt('limit');
            }
            else{
                return new Response("Parameter `limit` is in incorrect type format, `integer` is needed", 400, ['Content-Type', 'application/json']);
            }
        }

        $entities = $this->entityRepository->findAllWithParams($limit);

        if($entities){
            $serializer = new Serializer([new EntityNormalizer]);
            $data = $serializer->normalize([
                "entity_total" => count($entities),
                "limit" => $limit,
                "entities" => $entities
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