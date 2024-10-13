<?php
// src/Controller/Alt/CreateAlt.php
namespace App\Controller\Alt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Alt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Alt\CreateUpdateAltNormalizer;

class CreateAlt extends AltController
{
    #[Route(
        '/api/alt',
        name: 'create_alt',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getPayload();
        $params = [
            "name" => [
                "value" =>  $payload->get("name"),
                "type" => "string",
                "nullable" => false,
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
            }
        }

        if($this->altRepository->findOneBy(array("alt_name" => $params["name"]["value"]))){
            return new Response("This alt already exist", 400, ['Content-Type', 'application/json']);
        }

        $alt = new Alt();
        $alt->setAltName($params["name"]["value"]);

        foreach($params as $param){
            if($param["nullable"] === true){
                if($param["value"] !== null){
                    $method = $param["method"];
                    $alt->$method($param["value"]);
                }
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