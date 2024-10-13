<?php
// src/Controller/Alt/GroupAltBot.php
namespace App\Controller\Alt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Alt\AltNormalizer;
use App\Repository\BotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GroupAltBot extends AltController
{
    #[Route(
        '/api/alt/{altId}/{botId}',
        name: 'group_alt_bot',
        methods: ['POST'],
        requirements: ['altId' => '\d+', 'botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupBotToAlt(int $altId, int $botId, BotRepository $botRepository, EntityManagerInterface $entityManager): Response
    {
        $alt = $this->altRepository->find($altId);
        if(!$alt){
            return new Response("This alt doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bot = $botRepository->find($botId);
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bots = $alt->getBots();
        if($bots->contains($bot)){
            return new Response("This alt is already linked to this bot", 400, ['Content-Type', 'application/json']);
        }
        else{
            $alt->addBot($bot);
            $entityManager->persist($alt);
            $entityManager->flush();

            $serializer = new Serializer([new AltNormalizer]);
            $data = $serializer->normalize([
                "alt" => $alt
            ], "json");
            $json = $this->serializer->serialize($data, "json");
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/alt/{altId}/{botId}',
        name: 'ungroup_alt_bot',
        methods: ['DELETE'],
        requirements: ['altId' => '\d+', 'botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function removeBotFromAlt(int $altId, int $botId, BotRepository $botRepository, EntityManagerInterface $entityManager): Response
    {
        $alt = $this->altRepository->find($altId);
        if(!$alt){
            return new Response("This alt doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bot = $botRepository->find($botId);
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bots = $alt->getBots();
        if(!$bots->contains($bot)){
            return new Response("No link found between this alt and this bot", 404, ['Content-Type', 'application/json']);
        }
        else{
            $alt->removeBot($bot);
            $entityManager->persist($alt);
            $entityManager->flush();
            
            return new Response("This bot has been removed from this alt successfully");
        }
    }
}