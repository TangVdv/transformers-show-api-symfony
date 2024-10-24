<?php
// src/Controller/ScreenTime/UpdateScreenTime.php
namespace App\Controller\ScreenTime;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\ScreenTime\ScreenTimeNormalizer;

class UpdateScreenTime extends ScreenTimeController
{
    #[Route(
        '/api/screentime/{id}',
        name: 'update_screen_time',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $screentime = $this->screenTimeRepository->findOneBy(array("id" => $id));

        if(!$screentime){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "hour" => [
                "value" =>  $payload->get("hour"),
                "default" => $screentime->getHour(),
                "type" => "integer",
                "nullable" => true,
                "method" => "setHour"
            ],
            "minute" => [
                "value" => $payload->get("minute"),
                "default" => $screentime->getMinute(),
                "type" => "integer",
                "nullable" => true,
                "method" => "setMinute"
            ],
            "second" => [
                "value" => $payload->get("second"),
                "default" => $screentime->getSecond(),
                "type" => "integer",
                "nullable" => true,
                "method" => "setSecond"
            ]
        ];

        $modified = false;

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
                else{
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $screentime->$method($value["value"]);
                        $modified = true;
                    }
                }
            }
        }

        if($modified){
            $hour = $params["hour"]["value"] * 3600;
            $minute = $params["minute"]["value"] * 60;
            $total = $hour + $minute + $params["second"]["value"];

            $screentime->setTotal($total);
        }
    
        $entityManager->persist($screentime);
        $entityManager->flush();

        $serializer = new Serializer([new ScreenTimeNormalizer]);
        $data = $serializer->normalize([
            "screen_time" => $screentime
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}