<?php
// src/Controller/VoiceActor/VoiceActorController.php
namespace App\Controller\VoiceActor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\VoiceActorRepository;

abstract class VoiceActorController extends AbstractController
{
    protected VoiceActorRepository $voiceactorRepository;
    protected SerializerInterface $serializer;

    public function __construct(VoiceActorRepository $voiceactorRepository, SerializerInterface $serializer)
    {
        $this->voiceactorRepository = $voiceactorRepository;
        $this->serializer = $serializer;
    }
}