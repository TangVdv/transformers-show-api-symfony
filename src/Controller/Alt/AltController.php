<?php
// src/Controller/Alt/altController.php
namespace App\Controller\Alt;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\AltRepository;

abstract class AltController extends AbstractController
{
    protected AltRepository $altRepository;
    protected SerializerInterface $serializer;

    public function __construct(AltRepository $altRepository, SerializerInterface $serializer)
    {
        $this->altRepository = $altRepository;
        $this->serializer = $serializer;
    }
}