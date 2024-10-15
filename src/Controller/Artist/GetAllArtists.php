<?php
// src/Controller/Artist/GetAllArtists.php
namespace App\Controller\Artist;

use App\Normalizer\Artist\ArtistNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetAllArtists extends ArtistController
{
    #[Route(
        '/api/artists',
        name: 'get_artists',
        methods: ['GET']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request): Response
    {   
        $artists = $this->artistRepository->findAll();

        if($artists){
            $serializer = new Serializer([new ArtistNormalizer]);
            $data = $serializer->normalize([
                "artist_total" => count($artists),
                "artists" => $artists
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