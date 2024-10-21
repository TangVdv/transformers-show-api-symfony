<?php

namespace App\Normalizer\ConceptArt;

use App\Entity\ConceptArt;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateUpdateConceptArtNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "title" => $object->getTitle(),
            "note" => $object->getNote(),
            "image" => $object->getImage(),
            "srclink" => $object->getSrcLink(),
            "character" => null,
            "show" => $object->getShow() !== null ? $object->getShow()->getShowName() : null
        ];

        if($object->getEntity() !== null){
            $json["character"] = [
                "id" => $object->getEntity()->getId(),
                "name" => $object->getEntity()->getEntityName()
            ];
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