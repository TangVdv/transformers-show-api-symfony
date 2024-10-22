<?php
// src/Controller/VoiceLine/CreateVoiceLine.php
namespace App\Controller\VoiceLine;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\VoiceLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\EntityRepository;
use App\Repository\ShowRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\VoiceLine\VoiceLineNormalizer;

class CreateVoiceLine extends VoiceLineController
{
    #[Route(
        '/api/voiceline',
        name: 'create_voice_line',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, EntityRepository $entityRepository, ShowRepository $showRepository): Response
    {
        $payload = $request->getPayload();
        $params = [
            "entityId" => [
                "value" =>  $payload->get("entityId"),
                "type" => "integer",
                "nullable" => false
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "type" => "integer",
                "nullable" => false
            ],
            "content" => [
                "value" => $payload->get("content"),
                "type" => "string",
                "nullable" => false
            ],
            "number" => [
                "value" => $payload->get("number"),
                "type" => "integer",
                "nullable" => false
            ]
        ];

        $voiceline = new VoiceLine();

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
                        $voiceline->$method($value["value"]);
                    }
                }
            }
        }
        
        $entity = $entityRepository->find($params["entityId"]["value"]);
        if($entity === null){
            return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        
        $show = $showRepository->find($params["showId"]["value"]);
        if($show === null){
            return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        if($this->voiceLineRepository->findOneBy(array("show" => $show, "entity" => $entity, "number" => $params["number"]["value"]))){
            return new Response("A voice line with this number already exist with this bot in this show", 404, ['Content-Type', 'application/json']);
        }

        $voiceline
            ->setContent($params["content"]["value"])
            ->setNumber($params["number"]["value"])
            ->setEntity($entity)
            ->setShow($show);

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