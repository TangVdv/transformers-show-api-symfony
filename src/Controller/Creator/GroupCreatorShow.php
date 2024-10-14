<?php
// src/Controller/Creator/GroupCreatorShow.php
namespace App\Controller\Creator;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Creator\CreatorNormalizer;
use App\Repository\ShowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GroupCreatorShow extends CreatorController
{
    #[Route(
        '/api/creator/{creatorId}/{showId}',
        name: 'group_creator_show',
        methods: ['POST'],
        requirements: ['creatorId' => '\d+', 'showId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupCreatorToShow(int $creatorId, int $showId, ShowRepository $showRepository, EntityManagerInterface $entityManager): Response
    {
        $creator = $this->creatorRepository->find($creatorId);
        if(!$creator){
            return new Response("This creator doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $show = $showRepository->find($showId);
        if(!$show){
            return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $shows = $creator->getShows();
        if($shows->contains($show)){
            return new Response("This creator is already linked to this show", 400, ['Content-Type', 'application/json']);
        }
        else{
            $creator->addShow($show);
            $entityManager->persist($creator);
            $entityManager->flush();

            $serializer = new Serializer([new CreatorNormalizer]);
            $data = $serializer->normalize([
                "creaotr" => $creator
            ], "json");
            $json = $this->serializer->serialize($data, "json");
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/creator/{creatorId}/{showId}',
        name: 'ungroup_creator_show',
        methods: ['DELETE'],
        requirements: ['creatorId' => '\d+', 'showId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function removeCreatorFromShow(int $creatorId, int $showId, ShowRepository $showRepository, EntityManagerInterface $entityManager): Response
    {
        $creator = $this->creatorRepository->find($creatorId);
        if(!$creator){
            return new Response("This creator doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $show = $showRepository->find($showId);
        if(!$show){
            return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $shows = $creator->getShows();
        if(!$shows->contains($show)){
            return new Response("No link found between this creator and this show", 404, ['Content-Type', 'application/json']);
        }
        else{
            $creator->removeShow($show);
            $entityManager->persist($creator);
            $entityManager->flush();
            
            return new Response("This creator has been removed from this show successfully");
        }
    }
}