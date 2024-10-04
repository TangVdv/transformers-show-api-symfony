<?php

namespace App\Normalizer;

use App\Entity\Artist;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArtistNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getArtistFirstname(),
            "last_name" => $object->getArtistLastname(),
            "portfolio_link" => $object->getPortfolioLink()
        ];

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof Artist && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Artist::class => true
        ];
    }
}