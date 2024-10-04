<?php

namespace App\Normalizer;

use App\Entity\ConceptArt;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ConceptArtNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "title" => $object->getTitle(),
            "note" => $object->getNote(),
            "image" => $object->getImage(),
            "srclink" => $object->getSrclink(),
            "date" => $object->getDate(),
            "artist" => [],
            "character" => [
                "id" => $object->getEntity()->getId(),
                "name" => $object->getEntity()->getEntityName()
            ],
            "show" => $object->getShow()->getShowName()
        ];

        foreach($object->getArtists() as $artist){
            $a = [
                "id" => $artist->getId(),
                "firstname" => $artist->getArtistFirstname(),
                "lastname" => $artist->getArtistLastname(),
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