<?php

namespace App\Normalizer;

use App\Entity\ScreenTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ScreenTimeNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "hour" => $object->getHour(),
            "minute" => $object->getMinute(),
            "second" => $object->getSecond(),
            "total" => $object->getTotal(),
            "show" => $object->getShow()->getShowName()
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof ScreenTime && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ScreenTime::class => true
        ];
    }
}