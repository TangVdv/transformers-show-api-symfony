<?php
// src/Controller/VoiceLine/VoiceLineController.php
namespace App\Controller\VoiceLine;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\VoiceLineRepository;

abstract class VoiceLineController extends AbstractController
{
    protected VoiceLineRepository $voiceLineRepository;
    protected SerializerInterface $serializer;

    public function __construct(VoiceLineRepository $voiceLineRepository, SerializerInterface $serializer)
    {
        $this->voiceLineRepository = $voiceLineRepository;
        $this->serializer = $serializer;
    }
}