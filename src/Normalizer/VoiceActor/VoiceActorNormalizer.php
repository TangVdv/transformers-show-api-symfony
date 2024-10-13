<?php

namespace App\Normalizer\VoiceActor;

use App\Entity\VoiceActor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class VoiceActorNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getVoiceActorFirstname(),
            "last_name" => $object->getVoiceActorLastname(),
            "image" => $object->getImage(),
            "origin" => $object->getNationality() !== null ? $object->getNationality()->getCountry() : null,
            "bot" => []
        ];

        foreach($object->getBots() as $bot){
            $b = [
                "id" => $bot->getId(),
                "name" => $bot->getEntity() !== null ? $bot->getEntity()->getEntityName() : null,
                "image" => $bot->getImage(),
                "show" => $bot->getShow() !== null ? $bot->getShow()->getShowName() : null
            ];
            array_push($json["bot"], $b);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof VoiceActor && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            VoiceActor::class => true
        ];
    }
}