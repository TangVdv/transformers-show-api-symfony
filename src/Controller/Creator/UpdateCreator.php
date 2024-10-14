<?php
// src/Controller/Creator/UpdateCreator.php
namespace App\Controller\Creator;

use App\Normalizer\Creator\CreateUpdateCreatorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\NationalityRepository;

class UpdateCreator extends CreatorController
{
    #[Route(
        '/api/creator/{id}',
        name: 'update_creator',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, NationalityRepository $nationalityRepository): Response
    {
        $creator = $this->creatorRepository->findOneWithParams(array("id" => $id));

        if(!$creator){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }

        $categories = ["producer", "writer", "composer", "director"];

        $payload = $request->getPayload();
        $params = [
            "first_name" => [
                "value" =>  $payload->get("first_name"),
                "default" => $creator->getCreatorFirstname(),
                "type" => "string",
                "nullable" => true
            ],
            "last_name" => [
                "value" => $payload->get("last_name"),
                "default" => $creator->getCreatorLastname(),
                "type" => "string",
                "nullable" => true
            ],
            "category" => [
                "value" => $payload->get("category"),
                "type" => "string",
                "nullable" => true
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
                        $creator->$method($value["value"]);
                    }
                }
            }
        }

        if($params["category"]["value"] !== null){
            if(!in_array($params["category"]["value"], $categories)){
                return new Response("Parameter `category` has incorrect value. `producer`, `director`, `writer` or `composer` is authorized", 400, ['Content-Type', 'application/json']);
            }
            $creator->setCategory($params["category"]["value"]);
        }
        
        if($params["first_name"]["value"] !== $params["first_name"]["default"] || $params["last_name"]["value"] !== $params["last_name"]["default"]){
            if($this->creatorRepository->findOneBy(array("creator_firstname" => $params["first_name"]["value"], "creator_lastname" => $params["last_name"]["value"]))){
                return new Response("An creator with this name already exist", 400, ['Content-Type', 'application/json']);
            }
            $creator->setCreatorFirstname($params["first_name"]["value"])
                ->setCreatorLastname($params["last_name"]["value"]);
        }

        $entityManager->persist($creator);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateCreatorNormalizer]);
        $data = $serializer->normalize([
            "creator" => $creator
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}