<?php

namespace App\Normalizer\VoiceLine;

use App\Entity\VoiceLine;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class VoiceLineNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "content" => $object->getContent(),
            "number" => $object->getNumber(),
            "character" => $object->getEntity() !== null ? $object->getEntity()->getEntityName() : null,
            "show" => $object->getShow() !== null ? $object->getShow()->getShowName() : null
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof VoiceLine && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            VoiceLine::class => true
        ];
    }
}