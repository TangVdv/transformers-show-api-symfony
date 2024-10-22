<?php
// src/Controller/Entity/UpdateEntity.php
namespace App\Controller\Entity;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Entity\EntityNormalizer;

class UpdateEntity extends EntityController
{
    #[Route(
        '/api/entity/{id}',
        name: 'update_entity',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->entityRepository->findOneBy(array("id" => $id));

        if(!$entity){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "name" => [
                "value" =>  $payload->get("name"),
                "default" => $entity->getEntityName(),
                "type" => "string",
                "nullable" => true
            ],
            "image" => [
                "value" => $payload->get("image"),
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "type" => [
                "value" => $payload->get("type"),
                "type" => "integer",
                "nullable" => true,
                "method" => "setType"
            ]
        ];

        foreach($params as $key => &$value){
            if($value["value"] === null){
                if(!$value["nullable"]){
                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                }
                else{
                    if(array_key_exists("default", $value)){
                        $value["value"] = $value["default"];
                    }
                }
            }
            else{
                if(gettype($value["value"]) != $value["type"]){
                    return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
                }
                else{
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $entity->$method($value["value"]);
                    }
                }
            }
        }


        if($params["name"]["value"] !== $params["name"]["default"]){
            if($this->entityRepository->findOneBy(array("entity_name" => $params["name"]["value"]))){
                return new Response("An entity with this name already exist", 400, ['Content-Type', 'application/json']);
            }
            $entity->setEntityName($params["name"]["value"]);
        }
    
        $entityManager->persist($entity);
        $entityManager->flush();

        $serializer = new Serializer([new EntityNormalizer]);
        $data = $serializer->normalize([
            "entity" => $entity
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}