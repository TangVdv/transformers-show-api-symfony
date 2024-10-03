<?php
// src/Controller/Show/GetAllShows.php
namespace App\Controller\Show;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\AllShowsNormalizer;

class GetAllShows extends ShowController
{
    #[Route(
        '/api/shows',
        name: 'get_shows',
        methods: ['GET']
    )]
    public function __invoke(): Response
    {
        $shows = $this->showRepository->findAll();
        
        if($shows){
            $serializer = new Serializer([new AllShowsNormalizer]);
            $data = $serializer->normalize([
                "show_total" => count($shows),
                "shows" => $shows
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