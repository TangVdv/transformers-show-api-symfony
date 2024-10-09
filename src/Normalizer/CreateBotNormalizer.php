<?php

namespace App\Normalizer;

use App\Entity\Bot;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateBotNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntity()->getEntityName(),
            "description" => $object->getDescription(),
            "image" => $object->getImage(),
            "faction" => [],
            "transformation_count" => $object->getTransformationCount(),
            "alt_to_robot_count" => $object->getAltToRobot(),
            "robot_to_alt_count" => $object->getRobotToAlt(),
            "death_count" => $object->getDeathCount(),
            "kill_count" => $object->getKillCount(),
            "screen_time" => $object->getScreenTime() ? $object->getScreenTime()->getTotal() : null,
            "show" => $object->getShow()->getShowName()
        ];

        foreach($object->getFactions() as $faction){
            $f = [
                "name" => $faction->getFaction()->getFactionName(),
                "current" => $faction->getCurrent() == 1
            ];
            array_push($json["faction"], $f);
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