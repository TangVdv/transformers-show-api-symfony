<?php
// src/Controller/Artist/CreateArtist.php
namespace App\Controller\Artist;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Artist;
use App\Normalizer\Artist\ArtistNormalizer;

class CreateArtist extends ArtistController
{
    #[Route(
        '/api/artist',
        name: 'create_artist',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getPayload();
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
            "portfolio_link" => [
                "value" => $payload->get("portfolio_link"),
                "default" => null,
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
            }
        }

        if($this->artistRepository->findOneBy(
            array(
                "artist_firstname" => $params["first_name"]["value"], 
                "artist_lastname" => $params["last_name"]["value"]
        ))){
            return new Response("This artist already exist");
        }

        $artist = new Artist();
        $artist
            ->setArtistFirstname($params["first_name"]["value"])
            ->setArtistLastname($params["last_name"]["value"])
            ->setPortfolioLink($params["portfolio_link"]["value"]);
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