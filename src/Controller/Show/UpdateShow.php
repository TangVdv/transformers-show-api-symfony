<?php
// src/Controller/Show/UpdateShow.php
namespace App\Controller\Show;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Show\CreateUpdateShowNormalizer;

class UpdateShow extends ShowController
{
    #[Route(
        '/api/show/{id}',
        name: 'update_show',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $show = $this->showRepository->findOneBy(array("id" => $id));

        if(!$show){
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
                "value" => $payload->get("name"),
                "type" => "string",
                "nullable" => true
            ],
            "description" => [
                "value" =>  $payload->get("description"),
                "type" => "string",
                "nullable" => true,
                "method" => "setDescription"
            ],
            "release_date" => [
                "value" => $payload->get("release_date"),
                "type" => "string",
                "nullable" => true,
                "method" => "setReleaseDate"
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "running_time" => [
                "value" =>  $payload->get("running_time"),
                "type" => "integer",
                "nullable" => true,
                "method" => "setRunningTime"
            ],
            "budget" => [
                "value" =>  $payload->get("budget"),
                "type" => "integer",
                "nullable" => true,
                "method" => "setBudget"
            ],
            "box_office" => [
                "value" => $payload->get("box_office"),
                "type" => "integer",
                "nullable" => true,
                "method" => "setBoxOffice"
            ]
        ];

        foreach($params as $key => &$value){
            if(!empty($value["value"])){
                if(gettype($value["value"]) != $value["type"]){
                    return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
                }
                else{
                    if($value["type"] === "string"){
                        $value["value"] = preg_replace('/\s+/','', $value["value"]);
                    }
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $show->$method($value["value"]);
                    }
                } 
            }
        }

        if($params["name"]["value"] !== null){
            if($this->showRepository->findOneBy(array("show_name" => $params["name"]["value"]))){
                return new Response("A show with this name already exist", 400, ['Content-Type', 'application/json']);
            }
        }

        $entityManager->persist($show);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateShowNormalizer]);
        $data = $serializer->normalize([
            "show" => $show
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}