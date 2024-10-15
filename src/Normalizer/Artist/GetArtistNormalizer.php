<?php

namespace App\Normalizer\Artist;

use App\Entity\Artist;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetArtistNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "first_name" => $object->getArtistFirstname(),
            "last_name" => $object->getArtistLastname(),
            "portfolio_link" => $object->getPortfolioLink(),
            "concept_art" => []
        ];

        foreach($object->getConceptArts() as $concept_art){
            $ca = [
                "id" => $concept_art->getId(),
                "title" => $concept_art->getTitle(),
                "image" => $concept_art->getImage(),
                "srclink" => $concept_art->getSrclink()
            ];

            array_push($json["concept_art"], $ca);
        }

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