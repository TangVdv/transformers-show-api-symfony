<?php
// src/Controller/Artist/UpdateArtist.php
namespace App\Controller\Artist;

use App\Normalizer\Artist\ArtistNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;

class UpdateArtist extends ArtistController
{
    #[Route(
        '/api/artist/{id}',
        name: 'update_artist',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $artist = $this->artistRepository->findOneBy(array("id" => $id));

        if(!$artist){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "first_name" => [
                "value" =>  $payload->get("first_name"),
                "default" => $artist->getArtistFirstname(),
                "type" => "string",
                "nullable" => true
            ],
            "last_name" => [
                "value" => $payload->get("last_name"),
                "default" => $artist->getArtistLastname(),
                "type" => "string",
                "nullable" => true
            ],
            "portfolio_link" => [
                "value" => $payload->get("portfolio_link"),
                "type" => "string",
                "nullable" => true,
                "method" => "setPortfolioLink"
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
                        $artist->$method($value["value"]);
                    }
                }
            }
        }
        
        if($params["first_name"]["value"] !== $params["first_name"]["default"] || $params["last_name"]["value"] !== $params["last_name"]["default"]){
            if($this->artistRepository->findOneBy(array("artist_firstname" => $params["first_name"]["value"], "artist_lastname" => $params["last_name"]["value"]))){
                return new Response("An artist with this name already exist", 400, ['Content-Type', 'application/json']);
            }
            $artist->setArtistFirstname($params["first_name"]["value"])
                ->setArtistLastname($params["last_name"]["value"]);
        }

        $entityManager->persist($artist);
        $entityManager->flush();

        $serializer = new Serializer([new ArtistNormalizer]);
        $data = $serializer->normalize([
            "artist" => $artist
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}