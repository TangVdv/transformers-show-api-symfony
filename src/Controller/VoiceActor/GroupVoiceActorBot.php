<?php
// src/Controller/VoiceActor/GroupVoiceActorBot.php
namespace App\Controller\VoiceActor;

use App\Entity\VoiceActor;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\VoiceActor\VoiceActorNormalizer;
use App\Repository\BotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GroupVoiceActorBot extends VoiceActorController
{
    #[Route(
        '/api/voiceactor/{voiceactorId}/{botId}',
        name: 'group_voiceactor_bot',
        methods: ['POST'],
        requirements: ['voiceactorId' => '\d+', 'botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupBotToVoiceActor(int $voiceactorId, int $botId, BotRepository $botRepository, EntityManagerInterface $entityManager): Response
    {
        $voice_actor = $this->voiceactorRepository->find($voiceactorId);
        if(!$voice_actor){
            return new Response("This voice actor doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bot = $botRepository->find($botId);
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bots = $voice_actor->getBots();
        if($bots->contains($bot)){
            return new Response("This bot is already linked to this voice actor", 400, ['Content-Type', 'application/json']);
        }
        else{
            $voice_actor->addBot($bot);
            $entityManager->persist($voice_actor);
            $entityManager->flush();

            $serializer = new Serializer([new VoiceActorNormalizer]);
            $data = $serializer->normalize([
                "voiceactor" => $voice_actor
            ], "json");
            $json = $this->serializer->serialize($data, "json");
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/voiceactor/{voiceactorId}/{botId}',
        name: 'ungroup_voiceactor_bot',
        methods: ['DELETE'],
        requirements: ['voiceactorId' => '\d+', 'botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function removeBotFromVoiceActor(int $voiceactorId, int $botId, BotRepository $botRepository, EntityManagerInterface $entityManager): Response
    {
        $voice_actor = $this->voiceactorRepository->find($voiceactorId);
        if(!$voice_actor){
            return new Response("This voice actor doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bot = $botRepository->find($botId);
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $bots = $voice_actor->getBots();
        if(!$bots->contains($bot)){
            return new Response("No link found between this voice actor and this bot", 404, ['Content-Type', 'application/json']);
        }
        else{
            $voice_actor->removeBot($bot);
            $entityManager->persist($voice_actor);
            $entityManager->flush();
            
            return new Response("This bot has been removed from this voice actor successfully");
        }
    }
}