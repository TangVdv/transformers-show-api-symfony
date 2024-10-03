<?php

namespace App\Normalizer;

use App\Entity\Artefact;
use App\Entity\ScreenTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArtefactNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntity()->getEntityName(),
            "image" => $object->getEntity()->getImage(),
            "show" => []
        ];

        foreach($object->getScreenTimes() as $screen_time){
            $sc = [
                "id" => $screen_time->getShow()->getId(),
                "name" => $screen_time->getShow()->getShowName(),
                "screen_time" => $screen_time->getTotal()
            ];
            array_push($json["show"], $sc);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Artefact && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Artefact::class => true
        ];
    }
}