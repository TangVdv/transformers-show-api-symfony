<?php
// src/Controller/Artist/GetArtist.php
namespace App\Controller\Artist;

use App\Normalizer\Artist\GetArtistNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetArtist extends ArtistController
{
    #[Route(
        '/api/artist/{id}',
        name: 'get_artist_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function getArtistByID(int $id): Response
    {
        $artist = $this->artistRepository->findOneWithParams(array("id" => $id));
        
        if($artist){
            $serializer = new Serializer([new GetArtistNormalizer]);
            $data = $serializer->normalize([
                "artist" => $artist
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