<?php
// src/Controller/Human/HumanController.php
namespace App\Controller\Human;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\HumanRepository;

abstract class HumanController extends AbstractController
{
    protected HumanRepository $humanRepository;
    protected SerializerInterface $serializer;

    public function __construct(HumanRepository $humanRepository, SerializerInterface $serializer)
    {
        $this->humanRepository = $humanRepository;
        $this->serializer = $serializer;
    }
}