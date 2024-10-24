<?php
// src/Controller/ScreenTime/GroupScreenTime.php
namespace App\Controller\ScreenTime;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Normalizer\ScreenTime\CreateScreenTimeNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\ArtefactRepository;
use App\Repository\BotRepository;
use App\Repository\HumanRepository;

class GroupScreenTime extends ScreenTimeController
{
    #[Route(
        '/api/screentime/artefact/{screentimeId}/{artefactId}',
        name: 'group_screentime_artefact',
        methods: ['POST'],
        requirements: ['screentimeId' => '\d+', 'artefactId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupToArtefact(int $screentimeId, int $artefactId, EntityManagerInterface $entityManager, ArtefactRepository $artefactRepository): Response
    {
        $artefact = $artefactRepository->find($artefactId);
        $screentime = $this->screenTimeRepository->find($screentimeId);
        if(!$screentime){
            return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        if(!$artefact){
            return new Response("This artefact doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $artefact->setScreenTime($screentime);
            $screentime->addArtefact($artefact);

            $entityManager->persist($screentime);
            $entityManager->persist($artefact);
            $entityManager->flush();
            
    
            $serializer = new Serializer([new CreateScreenTimeNormalizer]);
            $data = $serializer->normalize([
                "screen_time" => $screentime
            ], "json", ["filter" => "artefact"]);
            $json = $this->serializer->serialize($data, 'json');
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/screentime/bot/{screentimeId}/{botId}',
        name: 'group_screentime_bot',
        methods: ['POST'],
        requirements: ['screentimeId' => '\d+', 'botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupToBot(int $screentimeId, int $botId, EntityManagerInterface $entityManager, BotRepository $botRepository): Response
    {
        $bot = $botRepository->find($botId);
        $screentime = $this->screenTimeRepository->find($screentimeId);
        if(!$screentime){
            return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $bot->setScreenTime($screentime);
            $screentime->addBot($bot);

            $entityManager->persist($screentime);
            $entityManager->persist($bot);
            $entityManager->flush();
            
    
            $serializer = new Serializer([new CreateScreenTimeNormalizer]);
            $data = $serializer->normalize([
                "screen_time" => $screentime
            ], "json", ["filter" => "bot"]);
            $json = $this->serializer->serialize($data, 'json');
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }

    #[Route(
        '/api/screentime/human/{screentimeId}/{humanId}',
        name: 'group_screentime_human',
        methods: ['POST'],
        requirements: ['screentimeId' => '\d+', 'humanId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function groupToHuman(int $screentimeId, int $humanId, EntityManagerInterface $entityManager, HumanRepository $humanRepository): Response
    {
        $human = $humanRepository->find($humanId);
        $screentime = $this->screenTimeRepository->find($screentimeId);
        if(!$screentime){
            return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        if(!$human){
            return new Response("This human doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $human->setScreenTime($screentime);
            $screentime->addHuman($human);

            $entityManager->persist($screentime);
            $entityManager->persist($human);
            $entityManager->flush();
            
    
            $serializer = new Serializer([new CreateScreenTimeNormalizer]);
            $data = $serializer->normalize([
                "screen_time" => $screentime
            ], "json", ["filter" => "human"]);
            $json = $this->serializer->serialize($data, 'json');
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
    }
}