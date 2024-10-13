<?php

namespace App\Normalizer\Alt;

use App\Entity\Alt;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateUpdateAltNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getAltName(),
            "image" => $object->getImage(),
            "brand" => $object->getBrand(),
            "model_year" => $object->getModelYear()
        ];

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