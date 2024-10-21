<?php

namespace App\Normalizer\ConceptArt;

use App\Entity\ConceptArt;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GroupArtistConceptArtNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "title" => $object->getTitle(),
            "artist" => [],
        ];

        if($object->getEntity() !== null){
            $json["character"] = [
                "id" => $object->getEntity()->getId(),
                "name" => $object->getEntity()->getEntityName()
            ];
        }

        foreach($object->getArtists() as $artist){
            $a = [
                "id" => $artist->getId(),
                "name" => $artist->getArtistFirstname()." ".$artist->getArtistLastname(),
                "portfolio_link" => $artist->getPortfolioLink()
            ];

            array_push($json["artist"], $a);
        }

        return $json;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof ConceptArt && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            ConceptArt::class => true
        ];
    }
}