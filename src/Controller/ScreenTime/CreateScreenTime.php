<?php
// src/Controller/ScreenTime/CreateScreenTime.php
namespace App\Controller\ScreenTime;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ScreenTime;
use App\Normalizer\ScreenTime\CreateScreenTimeNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\ArtefactRepository;
use App\Repository\BotRepository;
use App\Repository\HumanRepository;

class CreateScreenTime extends ScreenTimeController
{
    #[Route(
        '/api/screentime/artefact/{artefactId}',
        name: 'create_screentime_artefact',
        methods: ['POST'],
        requirements: ['artefactId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function createForArtefact(int $artefactId, Request $request, EntityManagerInterface $entityManager, ArtefactRepository $artefactRepository): Response
    {
        $artefact = $artefactRepository->find($artefactId);
        if(!$artefact){
            return new Response("This artefact doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $screentime = $this->prepareScreentime($request);
            if(get_class($screentime) === Response::class){
                return $screentime;
            }

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
        '/api/screentime/bot/{botId}',
        name: 'create_screentime_bot',
        methods: ['POST'],
        requirements: ['botId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function createForBot(int $botId, Request $request, EntityManagerInterface $entityManager, BotRepository $botRepository): Response
    {
        $bot = $botRepository->find($botId);
        if(!$bot){
            return new Response("This bot doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $screentime = $this->prepareScreentime($request);
            if(get_class($screentime) === Response::class){
                return $screentime;
            }

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
        '/api/screentime/human/{humanId}',
        name: 'create_screentime_human',
        methods: ['POST'],
        requirements: ['humanId' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function createForHuman(int $humanId, Request $request, EntityManagerInterface $entityManager, HumanRepository $humanRepository): Response
    {
        $human = $humanRepository->find($humanId);
        if(!$human){
            return new Response("This human doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        else{
            $screentime = $this->prepareScreentime($request);
            if(get_class($screentime) === Response::class){
                return $screentime;
            }

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

    public function prepareScreentime(Request $request): mixed
    {
        $payload = $request->getPayload();
        $params = [
            "hour" => [
                "value" =>  $payload->get("hour"),
                "type" => "integer",
                "nullable" => false,
                "method" => "setHour"
            ],
            "minute" => [
                "value" => $payload->get("minute"),
                "type" => "integer",
                "nullable" => false,
                "method" => "setMinute"
            ],
            "second" => [
                "value" => $payload->get("second"),
                "type" => "integer",
                "nullable" => false,
                "method" => "setSecond"
            ]
        ];

        $screentime = new ScreenTime();

        foreach($params as $key => &$value){
            if($value["value"] === null){
                if(!$value["nullable"]){
                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                }
            }
            else{
                if(gettype($value["value"]) != $value["type"]){
                    return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
                }
                else{
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $screentime->$method($value["value"]);
                    }
                }
            }
        }

        $hour = $params["hour"]["value"] * 3600;
        $minute = $params["minute"]["value"] * 60;
        $total = $hour + $minute + $params["second"]["value"];

        $screentime->setTotal($total);

        return $screentime;
    }
}