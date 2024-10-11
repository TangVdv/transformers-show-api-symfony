<?php
// src/Controller/Human/UpdateHuman.php
namespace App\Controller\Human;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ShowRepository;
use App\Repository\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Human\HumanNormalizer;
use App\Repository\ScreenTimeRepository;

class UpdateHuman extends HumanController
{
    #[Route(
        '/api/human/{id}',
        name: 'update_human',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, ShowRepository $showRepository, EntityRepository $entityRepository, ScreenTimeRepository $screenTimeRepository): Response
    {
        $human = $this->humanRepository->findOneWithParams(array("id" => $id));

        if(!$human){
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
                "default" => $human->getEntity()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "default" => $human->getShow()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "actorId" => [
                "value" => $payload->get("actorId"),
                "default" => $human->getActor()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "screen_timeId" => [
                "value" =>  $payload->get("screen_timeId"),
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
                        $human->$method($value["value"]);
                    }
                }
            }
        }

        if($params["screen_timeId"]["value"] !== null){
            $screen_time = $screenTimeRepository->find($params["screen_timeId"]["value"]);
            if($screen_time === null){
                return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $human->setScreenTime($screen_time);
        }

        $showModified = false;
        $actorModified = false;
        $entityModified = false;

        $show = $human->getShow();
        if($params["showId"]["value"] !== $params["showId"]["default"]){
            $show = $showRepository->find($params["showId"]["value"]);
            if($show === null){
                return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $showModified = true;
        }

        $entity = $human->getEntity();
        if($params["entityId"]["value"] !== $params["entityId"]["default"]){
            $entity = $entityRepository->find($params["entityId"]["value"]);
            if($entity === null){
                return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $entityModified = true;
        }

        $actor = $human->getActor();
        if($params["actorId"]["value"] !== $params["actorId"]["default"]){
            $actor = $entityRepository->find($params["actorId"]["value"]);
            if($actor === null){
                return new Response("This actor doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $actorModified = true;
        }

        if($showModified || $entityModified){
            if($this->humanRepository->findOneBy(array("entity" => $entity, "show" => $show))){
                return new Response("An entity already exist in this show", 400, ['Content-Type', 'application/json']);
            }
            $human->setShow($show)
                ->setEntity($entity);
        }

        if($showModified || $actorModified){
            if($this->humanRepository->findOneBy(array("actor" => $actor, "show" => $show))){
                return new Response("An actor already exist in this show", 400, ['Content-Type', 'application/json']);
            }
            $human->setShow($show)
                ->setActor($actor);
        }
    
        $entityManager->persist($human);
        $entityManager->flush();

        $serializer = new Serializer([new HumanNormalizer]);
        $data = $serializer->normalize([
            "human" => $human
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}