<?php

namespace App\Normalizer\Actor;

use App\Entity\Actor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ActorNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getActorFirstname(),
            "last_name" => $object->getActorLastname(),
            "image" => $object->getImage(),
            "origin" => $object->getNationality()->getCountry(),
            "character" => []
        ];

        foreach($object->getHumans() as $human){
            $h = [
                "id" => $human->getId(),
                "name" => $human->getEntity() !== null ? $human->getEntity()->getEntityName() : null,
                "image" => $human->getImage(),
                "show" => $human->getShow() !== null ? $human->getShow()->getShowName() : null
            ];
            array_push($json["character"], $h);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Actor && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Actor::class => true
        ];
    }
}