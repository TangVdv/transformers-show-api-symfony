<?php

namespace App\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{


    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        return [
            "id" => $object->getId(),
            "uuid" => $object->getUuid(),
            "username" => $object->getUsername(),
            "email" => $object->getEmail(),
            "email_verified" => $object->getEmailVerified(),
            "created_at" => $object->getCreatedAt(),
            "updated_at" => $object->getUpdatedAt()
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof User && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            User::class => true
        ];
    }
}