<?php

namespace App\Normalizer\Human;

use App\Entity\Human;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HumanNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntity()->getEntityName(),
            "image" => $object->getImage(),
            "actor" => [
                "name" => $object->getActor()->getActorFirstname()." ".$object->getActor()->getActorLastname(),
                "origin" => $object->getActor()->getNationality()->getCountry()
            ],
            "screen_time" => $object->getScreenTime() ? $object->getScreenTime()->getTotal() : null,
            "show" => [
                "id" => $object->getShow()->getId(),
                "name" => $object->getShow()->getShowName()
            ]
        ];

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