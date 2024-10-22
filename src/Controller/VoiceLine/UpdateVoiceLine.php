<?php
// src/Controller/VoiceLine/UpdateVoiceLine.php
namespace App\Controller\VoiceLine;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ShowRepository;
use App\Repository\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\VoiceLine\VoiceLineNormalizer;

class UpdateVoiceLine extends VoiceLineController
{
    #[Route(
        '/api/voiceline/{id}',
        name: 'update_voice_line',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, ShowRepository $showRepository, EntityRepository $entityRepository): Response
    {
        $voiceline = $this->voiceLineRepository->findOneBy(array("id" => $id));

        if(!$voiceline){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "entityId" => [
                "value" =>  $payload->get("entityId"),
                "default" => $voiceline->getEntity()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "default" => $voiceline->getShow()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "content" => [
                "value" => $payload->get("content"),
                "type" => "string",
                "nullable" => true,
                "method" => "setContent"
            ],
            "number" => [
                "value" => $payload->get("number"),
                "default" => $voiceline->getNumber(),
                "type" => "integer",
                "nullable" => true
            ]
        ];

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
                        $voiceline->$method($value["value"]);
                    }
                }
            }
        }

        $showModified = false;
        $entityModified = false;
        $numberModified = false;

        $show = $voiceline->getShow();
        if($params["showId"]["value"] !== $params["showId"]["default"]){
            $show = $showRepository->find($params["showId"]["value"]);
            if($show === null){
                return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $showModified = true;
        }

        $entity = $voiceline->getEntity();
        if($params["entityId"]["value"] !== $params["entityId"]["default"]){
            $entity = $entityRepository->find($params["entityId"]["value"]);
            if($entity === null){
                return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $entityModified = true;
        }

        if($params["number"]["value"] !== $params["number"]["default"]){
            $numberModified = true;
        }

        if($showModified || $entityModified || $numberModified){
            if($this->voiceLineRepository->findOneBy(array("show" => $show, "entity" => $entity, "number" => $params["number"]["value"]))){
                return new Response("A voice line with this number already exist with this bot in this show", 404, ['Content-Type', 'application/json']);
            }
            $voiceline->setNumber($params["number"]["value"])
                ->setShow($show)
                ->setEntity($entity);
        }
    
        $entityManager->persist($voiceline);
        $entityManager->flush();

        $serializer = new Serializer([new VoiceLineNormalizer]);
        $data = $serializer->normalize([
            "voice_line" => $voiceline
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}