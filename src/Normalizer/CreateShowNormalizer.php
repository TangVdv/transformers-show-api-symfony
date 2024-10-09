<?php

namespace App\Normalizer;

use App\Entity\Show;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateShowNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "name" => $object->getShowName(),
            "description" => $object->getDescription(),
            "release_date" => $object->getReleaseDate(),
            "image" => $object->getImage(),
            "running_time" => $object->getRunningTime(),
            "budget" => $object->getBudget(),
            "box_office" => $object->getBoxOffice()
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Show && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Show::class => true
        ];
    }
}