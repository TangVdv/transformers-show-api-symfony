<?php
// src/Controller/Bot/CreateBot.php
namespace App\Controller\Bot;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Bot;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\EntityRepository;
use App\Repository\ShowRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Bot\CreateUpdateBotNormalizer;
use App\Repository\FactionRepository;
use App\Entity\Membership;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\MembershipRepository;

class CreateBot extends BotController
{
    #[Route(
        '/api/bot',
        name: 'create_bot',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, EntityRepository $entityRepository, ShowRepository $showRepository, FactionRepository $factionRepository, MembershipRepository $membershipRepository): Response
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
            "description" => [
                "value" =>  $payload->get("description"),
                "default" => "",
                "type" => "string",
                "nullable" => true
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true
            ],
            "screen_time" => [
                "value" =>  $payload->get("screen_time"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "alt_to_robot" => [
                "value" => $payload->get("alt_to_robot_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "robot_to_alt" => [
                "value" => $payload->get("robot_to_alt_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "death_count" => [
                "value" => $payload->get("death_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "kill_count" => [
                "value" => $payload->get("kill_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "faction" => [
                "value" => $payload->getNonScalar("faction"),
                "default" => [],
                "type" => "array",
                "params" => [
                    "name" => [
                        "type" => "string",
                        "nullable" => false
                    ],
                    "current" => [
                        "type" => "boolean",
                        "nullable" => false
                    ],
                ],
                "nullable" => true
            ]
        ];

        foreach($params as $key => &$value){
            if($value["value"] === null){
                if(!$value["nullable"]){
                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                }
                else{
                    $value["value"] = $value["default"];
                }
            }
            if(gettype($value["value"]) != $value["type"]){
                return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
            }
            else{
                if($value["type"] === "array" && count($value["value"]) > 0){
                    foreach($value["value"] as $arr){
                        foreach($value["params"] as $key => &$val){
                            if(!array_key_exists($key, $arr)){
                                if(!$value["params"][$key]["nullable"]){
                                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                                }
                                else{
                                    $val = $value["params"][$key]["default"];
                                }
                            }

                            if(gettype($arr[$key]) != $value["params"][$key]["type"]){
                                return new Response("Parameter `{$key}` is in incorrect type format, `{$value["params"][$key]["type"]}` is needed", 400, ['Content-Type', 'application/json']);
                            }
                        }
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

        $factions = new ArrayCollection();
        if(count($params["faction"]["value"]) > 0){
            foreach($params["faction"]["value"] as $arr){
                $faction = $factionRepository->findOneBy(array("faction_name" => $arr["name"]));
                if($faction === null){
                    return new Response("This faction doesn't exist : `{$arr["name"]}`", 404, ['Content-Type', 'application/json']);
                }
                else{
                    $membership = new Membership();
                    $membership->setFaction($faction)
                            ->setCurrent($arr["current"]);
                    if(!$factions->contains($membership)){
                        $factions->add($membership);
                    }
                }
            }
        }

        if($this->botRepository->findOneBy(
            array(
                "entity" => $entity, 
                "show" => $show
        ))){
            return new Response("This bot already exist");
        }

        $transformation_count = $params["alt_to_robot"]["value"] + $params["robot_to_alt"]["value"];
        $bot = new Bot();
        $bot->setDescription($params["description"]["value"])
            ->setImage($params["image"]["value"])
            ->setTransformationCount($transformation_count)
            ->setAltToRobot($params["alt_to_robot"]["value"])
            ->setRobotToAlt($params["robot_to_alt"]["value"])
            ->setDeathCount($params["death_count"]["value"])
            ->setKillCount($params["kill_count"]["value"])
            ->setEntity($entity)
            ->setShow($show);

        $entityManager->persist($bot);
        $entityManager->flush();

        if(count($factions) > 0){
            foreach($factions as $faction){
                $faction->setBot($bot);
                $entityManager->persist($faction);
                $entityManager->flush();   
            }
        }

        $serializer = new Serializer([new CreateUpdateBotNormalizer]);
        $data = $serializer->normalize([
            "bot" => $bot
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}