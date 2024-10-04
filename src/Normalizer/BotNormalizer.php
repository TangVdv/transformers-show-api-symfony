<?php

namespace App\Normalizer;

use App\Entity\Bot;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BotNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntity()->getEntityName(),
            "description" => $object->getDescription(),
            "image" => $object->getImage(),
            "show" => [ 
                "id" => $object->getScreenTime()->getShow()->getId(),
                "name" => $object->getScreenTime()->getShow()->getShowName()
            ],
            "screen_time" => $object->getScreenTime()->getTotal(),
            "faction" => [],
            "transformation_count" => $object->getTransformationCount(),
            "alt_to_robot_count" => $object->getAltToRobot(),
            "robot_to_alt_count" => $object->getRobotToAlt(),
            "death_count" => $object->getDeathCount(),
            "kill_count" => $object->getKillCount(),
            "alt" => [],
            "voiceactor" => []
        ];

        foreach($object->getFactions() as $faction){
            $f = [
                "name" => $faction->getFaction()->getFactionName(),
                "current" => $faction->getCurrent() == 1
            ];
            array_push($json["faction"], $f);
        }

        foreach($object->getAlts() as $alt){
            $a = [
                "id" => $alt->getId(),
                "name" => $alt->getAltName(),
                "image" => $alt->getImage()
            ];
            array_push($json["alt"], $a);
        }

        foreach($object->getVoiceActors() as $voiceactor){
            $va = [
                "id" => $voiceactor->getId(),
                "name" => $voiceactor->getVoiceActorFirstname()." ".$voiceactor->getVoiceActorLastname(),
                "image" => $voiceactor->getImage(),
                "origin" => $voiceactor->getNationality()->getCountry()
            ];
            array_push($json["voiceactor"], $va);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Bot && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Bot::class => true
        ];
    }
}