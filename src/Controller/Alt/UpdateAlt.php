<?php
// src/Controller/Alt/UpdateAlt.php
namespace App\Controller\Alt;

use App\Normalizer\Alt\CreateUpdateAltNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;

class UpdateAlt extends AltController
{
    #[Route(
        '/api/alt/{id}',
        name: 'update_alt',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $alt = $this->altRepository->findOneWithParams(array("id" => $id));

        if(!$alt){
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
                "type" => "string",
                "nullable" => true
            ],
            "image" => [
                "value" => $payload->get("image"),
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "brand" => [
                "value" => $payload->get("brand"),
                "type" => "string",
                "nullable" => true,
                "method" => "setBrand"
            ],
            "model_year" => [
                "value" =>  $payload->get("model_year"),
                "type" => "integer",
                "nullable" => true,
                "method" => "setModelYear"
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
                        $alt->$method($value["value"]);
                    }
                }
            }
        }

        if($params["name"]["value"] !== null){
            if($this->altRepository->findOneBy(array("alt_name" => $params["name"]["value"]))){
                return new Response("An alt already exist with this name", 400, ['Content-Type', 'application/json']);
            }
            else{
                $alt->setAltName($params["name"]["value"]);
            }
        }
    
        $entityManager->persist($alt);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateAltNormalizer]);
        $data = $serializer->normalize([
            "alt" => $alt
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}