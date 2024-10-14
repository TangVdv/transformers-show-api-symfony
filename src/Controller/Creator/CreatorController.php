<?php
// src/Controller/Creator/CreatorController.php
namespace App\Controller\Creator;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\CreatorRepository;

abstract class CreatorController extends AbstractController
{
    protected CreatorRepository $creatorRepository;
    protected SerializerInterface $serializer;

    public function __construct(CreatorRepository $creatorRepository, SerializerInterface $serializer)
    {
        $this->creatorRepository = $creatorRepository;
        $this->serializer = $serializer;
    }
}