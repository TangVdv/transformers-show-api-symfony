<?php

namespace App\Normalizer\Creator;

use App\Entity\Creator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateUpdateCreatorNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getCreatorFirstname(),
            "last_name" => $object->getCreatorLastname(),
            "category" => $object->getCategory()
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Creator && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Creator::class => true
        ];
    }
}