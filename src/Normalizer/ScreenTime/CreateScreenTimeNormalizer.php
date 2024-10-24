<?php

namespace App\Normalizer\ScreenTime;

use App\Entity\ScreenTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateScreenTimeNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "hour" => $object->getHour(),
            "minute" => $object->getMinute(),
            "second" => $object->getSecond(),
            "total" => $object->getTotal()
        ];

        if(array_key_exists("filter", $context)){
            $filter = $context["filter"];
            if($filter === "artefact"){
                $json["artefact"] = [];
                foreach($object->getArtefacts() as $artefact){
                    $a = [
                        "id" => $artefact->getId(),
                        "name" => $artefact->getEntity() !== null ? $artefact->getEntity()->getEntityName() : null,
                        "image" => $artefact->getImage(),
                        "screen_time" => $artefact->getScreenTime()->getTotal(),
                        "show" => $artefact->getShow() !== null ? $artefact->getShow()->getShowName() : null
                    ];
                    array_push($json["artefact"], $a);
                }
            }
            else if($filter === "bot"){
                $json["bot"] = [];
                foreach($object->getBots() as $bot){
                    $b = [
                        "id" => $bot->getId(),
                        "name" => $bot->getEntity() !== null ? $bot->getEntity()->getEntityName() : null,
                        "image" => $bot->getImage(),
                        "screen_time" => $bot->getScreenTime()->getTotal(),
                        "show" => $bot->getShow() !== null ? $bot->getShow()->getShowName() : null
                    ];
                    array_push($json["bot"], $b);
                }
            }
            else if($filter === "human"){
                $json["human"] = [];
                foreach($object->getHumans() as $human){
                    $h = [
                        "id" => $human->getId(),
                        "name" => $human->getEntity() !== null ? $human->getEntity()->getEntityName() : null,
                        "image" => $human->getImage(),
                        "screen_time" => $human->getScreenTime()->getTotal(),
                        "show" => $human->getShow() !== null ? $human->getShow()->getShowName() : null
                    ];
                    array_push($json["human"], $h);
                }
            }
        }

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