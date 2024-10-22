<?php

namespace App\Normalizer\Entity;

use App\Entity\Entity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EntityNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getEntityName(),
            "image" => $object->getImage(),
            "type" => $object->getType(),
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Entity && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Entity::class => true
        ];
    }
}