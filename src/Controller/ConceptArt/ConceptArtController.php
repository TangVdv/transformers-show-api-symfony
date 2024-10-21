<?php
// src/Controller/ConceptArt/ConceptArtController.php
namespace App\Controller\ConceptArt;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ConceptArtRepository;

abstract class ConceptArtController extends AbstractController
{
    protected ConceptArtRepository $conceptArtRepository;
    protected SerializerInterface $serializer;

    public function __construct(ConceptArtRepository $conceptArtRepository, SerializerInterface $serializer)
    {
        $this->conceptArtRepository = $conceptArtRepository;
        $this->serializer = $serializer;
    }
}