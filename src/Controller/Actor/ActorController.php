<?php
// src/Controller/Actor/ActorController.php
namespace App\Controller\Actor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ActorRepository;

abstract class ActorController extends AbstractController
{
    protected ActorRepository $actorRepository;
    protected SerializerInterface $serializer;

    public function __construct(ActorRepository $actorRepository, SerializerInterface $serializer)
    {
        $this->actorRepository = $actorRepository;
        $this->serializer = $serializer;
    }
}