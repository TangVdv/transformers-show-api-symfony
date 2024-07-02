<?php

namespace App\Normalizer;

use App\Entity\User;
use App\Normalizer\UserAuthNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    protected UserAuthNormalizer $userAuthNormalizer;

    public function __construct(UserAuthNormalizer $normalizer){
        $this->userAuthNormalizer = $normalizer;
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {        
        $json = [
            "id" => $object->getId(),
            "uuid" => $object->getUuid(),
            "username" => $object->getFullName(),
            "email" => $object->getEmail(),
            "email_verified" => $object->getEmailVerified(),
            "created_at" => $object->getCreatedAt(),
            "updated_at" => $object->getUpdatedAt()
        ];

        if($object->getUserAuth()){
            $json["token"] = $this->userAuthNormalizer->normalize($object->getUserAuth());
        }

        return $json;
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