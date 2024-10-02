<?php

namespace App\Normalizer;

use App\Entity\Show;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShowNormalizer implements NormalizerInterface
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
            "box_office" => $object->getBoxOffice(),
            "director_name" => $object->getDirector(),
            "producer_name" => $object->getProducer(),
            "writer_name" => $object->getWriter(),
            "composer_name" => $object->getComposer(),
            "bot" => $object->getBot(),
            "human" => $object->getHuman(),
            "concept_art" => $object->getConceptArt(),
            "voice_line" => $object->getVoiceLine(),
            "artefact" => $object->getArtefact()
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