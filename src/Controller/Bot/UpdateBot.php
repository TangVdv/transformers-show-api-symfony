<?php
// src/Controller/Bot/UpdateBot.php
namespace App\Controller\Bot;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\MembershipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Membership;
use App\Repository\FactionRepository;
use App\Repository\ShowRepository;
use App\Repository\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Bot\CreateUpdateBotNormalizer;
use App\Repository\ScreenTimeRepository;

class UpdateBot extends BotController
{
    #[Route(
        '/api/bot/{id}',
        name: 'update_bot',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, MembershipRepository $membershipRepository, FactionRepository $factionRepository, ShowRepository $showRepository, EntityRepository $entityRepository, ScreenTimeRepository $screenTimeRepository): Response
    {
        $bot = $this->botRepository->findOneWithParams(array("id" => $id));

        if(!$bot){
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
                "default" => $bot->getEntity()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "default" => $bot->getShow()->getId(),
                "type" => "integer",
                "nullable" => true
            ],
            "description" => [
                "value" =>  $payload->get("description"),
                "default" => "",
                "type" => "string",
                "nullable" => true,
                "method" => "setDescription"
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
            ],
            "screen_time" => [
                "value" =>  $payload->get("screen_timeId"),
                "type" => "integer",
                "nullable" => true
            ],
            "alt_to_robot" => [
                "value" => $payload->get("alt_to_robot_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true,
                "method" => "setAltToRobot"
            ],
            "robot_to_alt" => [
                "value" => $payload->get("robot_to_alt_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true,
                "method" => "setRobotToAlt"
            ],
            "death_count" => [
                "value" => $payload->get("death_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true,
                "method" => "setDeathCount"
            ],
            "kill_count" => [
                "value" => $payload->get("kill_count"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true,
                "method" => "setKillCount"
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
                    else{
                        if(array_key_exists("method", $value)){
                            $method = $value["method"];
                            $bot->$method($value["value"]);
                        }
                    }
                }
            }
        }

        if($params["screen_time"]["value"] !== null){
            $screen_time = $screenTimeRepository->find($params["screen_time"]["value"]);
            if($screen_time === null){
                return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            else{
                $bot->setScreenTime($screen_time);
            }
        }
        

        if($params["entityId"]["value"] !== $params["entityId"]["default"] && $params["showId"]["value"] !== $params["showId"]["default"]){
          $entity = $entityRepository->find($params["entityId"]["value"]);
            if($entity === null){
                return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
            }

            $show = $showRepository->find($params["showId"]["value"]);
            if($show === null){
                return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
            }

            if($this->botRepository->findOneBy(array("entity" => $entity, "show" => $show))){
                return new Response("An entity already exist in this show", 400, ['Content-Type', 'application/json']);
            }
            else{
                $bot->setShow($show)
                    ->setEntity($entity);
            }  
        }
        

        //creates the membership for each given faction
        $preparedMemberships = new ArrayCollection();
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
                    if(!$preparedMemberships->contains($membership)){
                        $preparedMemberships->add($membership);
                    }
                }
            }
        }


        $memberships = $membershipRepository->findAll(array(
            "bot" => $bot
        ));

        $currentMemberships = new ArrayCollection();
        $removeMembershipCollection = new ArrayCollection();

        foreach($memberships as $membership){
            $currentMemberships->add($membership->getFaction());
            $removeMembershipCollection->add($membership);
        }

        foreach($preparedMemberships as $preparedMembership){
            $faction = $preparedMembership->getFaction();

            //binds the prepared membership to the bot
            if(!$currentMemberships->contains($faction)){
                $preparedMembership->setBot($bot);
                $entityManager->persist($preparedMembership);

                $bot->addMembership($preparedMembership);
            }
            else{
                foreach($removeMembershipCollection as $membership){
                    //updates the current state of each membership if there is a difference between prepared and own factions
                    if($membership->getFaction() === $faction){
                        if($membership->getCurrent() !== $preparedMembership->getCurrent()){
                            $membership->setCurrent($preparedMembership->getCurrent());
                            $entityManager->persist($membership);
                        }
                        //removes the current membership from the removing collection
                        $removeMembershipCollection->removeElement($membership);
                    }
                }
            }
        }

        foreach($removeMembershipCollection as $membership){
            $entityManager->remove($membership); 
        }

        $entityManager->persist($bot);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateBotNormalizer]);
        $data = $serializer->normalize([
            "bot" => $bot
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}