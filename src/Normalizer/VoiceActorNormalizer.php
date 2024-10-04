<?php

namespace App\Normalizer;

use App\Entity\VoiceActor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class VoiceActorNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getVoiceactorFirstname(),
            "last_name" => $object->getVoiceactorLastname(),
            "image" => $object->getImage(),
            "origin" => $object->getNationality()->getCountry(),
            "bot" => []
        ];

        foreach($object->getBots() as $bot){
            $b = [
                "id" => $bot->getId(),
                "name" => $bot->getEntity()->getEntityName(),
                "image" => $bot->getImage(),
                "show" => $bot->getScreenTime()->getShow()->getShowName()
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