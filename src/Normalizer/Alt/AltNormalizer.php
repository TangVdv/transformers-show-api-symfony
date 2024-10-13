<?php

namespace App\Normalizer\Alt;

use App\Entity\Alt;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AltNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getAltName(),
            "image" => $object->getImage(),
            "brand" => $object->getBrand(),
            "model_year" => $object->getModelYear(),
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
        return $data instanceof Alt && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Alt::class => true
        ];
    }
}