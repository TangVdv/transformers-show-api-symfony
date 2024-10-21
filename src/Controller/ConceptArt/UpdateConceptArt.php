<?php
// src/Controller/ConceptArt/UpdateConceptArt.php
namespace App\Controller\ConceptArt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ShowRepository;
use App\Repository\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\ConceptArt\CreateUpdateConceptArtNormalizer;

class UpdateConceptArt extends ConceptArtController
{
    #[Route(
        '/api/conceptart/{id}',
        name: 'update_concept_art',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, ShowRepository $showRepository, EntityRepository $entityRepository): Response
    {
        $conceptart = $this->conceptArtRepository->findOneWithParams(array("id" => $id));

        if(!$conceptart){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "entityId" => [
                "value" =>  $payload->get("entityId"),
                "default" => $conceptart->getEntity()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "default" => $conceptart->getShow()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "title" => [
                "value" => $payload->get("title"),
                "type" => "string",
                "nullable" => true,
                "method" => "setTitle"
            ],
            "image" => [
                "value" => $payload->get("image"),
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "note" => [
                "value" => $payload->get("note"),
                "type" => "string",
                "nullable" => true,
                "method" => "setNote"
            ],
            "srclink" => [
                "value" =>  $payload->get("srclink"),
                "type" => "string",
                "nullable" => true,
                "method" => "setSrcLink"
            ],
            "date" => [
                "value" =>  $payload->get("date"),
                "type" => "string",
                "nullable" => true,
                "method" => "setDate"
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
                        $conceptart->$method($value["value"]);
                    }
                }
            }
        }

        $showModified = false;
        $entityModified = false;

        $show = $conceptart->getShow();
        if($params["showId"]["value"] !== $params["showId"]["default"]){
            $show = $showRepository->find($params["showId"]["value"]);
            if($show === null){
                return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $showModified = true;
        }

        $entity = $conceptart->getEntity();
        if($params["entityId"]["value"] !== $params["entityId"]["default"]){
            $entity = $entityRepository->find($params["entityId"]["value"]);
            if($entity === null){
                return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $entityModified = true;
        }

        if($showModified || $entityModified){
            $conceptart->setShow($show)
                ->setEntity($entity);
        }
    
        $entityManager->persist($conceptart);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateConceptArtNormalizer]);
        $data = $serializer->normalize([
            "concept_art" => $conceptart
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}