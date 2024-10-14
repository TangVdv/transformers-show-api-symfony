<?php
// src/Controller/Creator/CreateCreator.php
namespace App\Controller\Creator;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\NationalityRepository;
use App\Entity\Creator;
use App\Normalizer\Creator\CreateUpdateCreatorNormalizer;

class CreateCreator extends CreatorController
{
    #[Route(
        '/api/creator',
        name: 'create_creator',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, NationalityRepository $nationalityRepository): Response
    {
        $payload = $request->getPayload();

        $categories = ["producer", "writer", "composer", "director"];

        $params = [
            "first_name" => [
                "value" =>  $payload->get("first_name"),
                "type" => "string",
                "nullable" => false
            ],
            "last_name" => [
                "value" => $payload->get("last_name"),
                "type" => "string",
                "nullable" => false
            ],
            "category" => [
                "value" =>  $payload->get("category"),
                "type" => "string",
                "nullable" => false
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
            }
        }

        if(!in_array($params["category"]["value"], $categories)){
            return new Response("Parameter `category` has incorrect value. `producer`, `director`, `writer` or `composer` is authorized", 400, ['Content-Type', 'application/json']);
        }

        if($this->creatorRepository->findOneBy(
            array(
                "creator_firstname" => $params["first_name"]["value"], 
                "creator_lastname" => $params["last_name"]["value"]
        ))){
            return new Response("This creator already exist");
        }

        $creator = new Creator();
        $creator
            ->setCreatorFirstname($params["first_name"]["value"])
            ->setCreatorLastname($params["last_name"]["value"])
            ->setCategory($params["category"]["value"]);
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