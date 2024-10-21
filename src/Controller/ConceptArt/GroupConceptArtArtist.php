<?php
// src/Controller/ConceptArt/GroupConceptArtArtist.php
namespace App\Controller\ConceptArt;

use App\Normalizer\ConceptArt\GroupArtistConceptArtNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GroupConceptArtArtist extends ConceptArtController
{
    #[Route(
        '/api/conceptart/{conceptartId}/{artistId}',
        name: 'group_concept_art_artist',
        methods: ['POST'],
        requirements: ['conceptartId' => '\d+', 'artistId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupArtistToConceptArt(int $conceptartId, int $artistId, ArtistRepository $artistRepository, EntityManagerInterface $entityManager): Response
    {
        $conceptart = $this->conceptArtRepository->find($conceptartId);
        if(!$conceptart){
            return new Response("This concept art doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $artist = $artistRepository->find($artistId);
        if(!$artist){
            return new Response("This artist doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $artists = $conceptart->getArtists();
        if($artists->contains($artist)){
            return new Response("This artist is already linked to this concept art", 400, ['Content-Type', 'application/json']);
        }
        else{
            $conceptart->addArtist($artist);
            $entityManager->persist($conceptart);
            $entityManager->flush();

            $serializer = new Serializer([new GroupArtistConceptArtNormalizer]);
            $data = $serializer->normalize([
                "concept_art" => $conceptart
            ], "json");
            $json = $this->serializer->serialize($data, "json");
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/conceptart/{conceptartId}/{artistId}',
        name: 'ungroup_concept_art_artist',
        methods: ['DELETE'],
        requirements: ['conceptartId' => '\d+', 'artistId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function removeArtistFromConceptArt(int $conceptartId, int $artistId, ArtistRepository $artistRepository, EntityManagerInterface $entityManager): Response
    {
        $conceptart = $this->conceptArtRepository->find($conceptartId);
        if(!$conceptart){
            return new Response("This concept art doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $artist = $artistRepository->find($artistId);
        if(!$artist){
            return new Response("This artist doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $artists = $conceptart->getArtists();
        if(!$artists->contains($artist)){
            return new Response("No link found between this artist and this concept art", 404, ['Content-Type', 'application/json']);
        }
        else{
            $conceptart->removeArtist($artist);
            $entityManager->persist($conceptart);
            $entityManager->flush();
            
            return new Response("This artist has been removed from this concept art successfully");
        }
    }
}