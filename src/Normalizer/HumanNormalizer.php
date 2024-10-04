<?php

namespace App\Normalizer;

use App\Entity\Human;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HumanNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntity()->getEntityName(),
            "image" => $object->getEntity()->getImage(),
            "actor" => [
                "name" => $object->getActor()->getActorFirstname()." ".$object->getActor()->getActorLastname(),
                "origin" => $object->getActor()->getNationality()->getCountry()
            ],
            "show" => []
        ];

        foreach($object->getScreenTimes() as $screen_time){
            $s = [
                "id" => $screen_time->getShow()->getId(),
                "name" => $screen_time->getShow()->getShowName(),
                "screen_time" => $screen_time->getTotal()
            ];
            array_push($json["show"], $s);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Human && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Human::class => true
        ];
    }
}