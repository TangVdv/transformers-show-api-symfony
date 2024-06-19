<?php

namespace App\Normalizer;

use App\Entity\UserAuth;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserAuthNormalizer implements NormalizerInterface
{


    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        return [
            "issued_at" => $object->getIssuedAt(),
            "expires_in" => $object->getExpiresIn(),
            "token_type" => $object->getTokenType(),
            "access_token" => $object->getAccessToken(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []):bool
    {
        return $data instanceof UserAuth && $format == 'json';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            UserAuth::class => true
        ];
    }
}