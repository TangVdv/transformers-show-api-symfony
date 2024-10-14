<?php
// src/Controller/Artefact/ArtefactController.php
namespace App\Controller\Artefact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ArtefactRepository;

abstract class ArtefactController extends AbstractController
{
    protected ArtefactRepository $artefactRepository;
    protected SerializerInterface $serializer;

    public function __construct(ArtefactRepository $artefactRepository, SerializerInterface $serializer)
    {
        $this->artefactRepository = $artefactRepository;
        $this->serializer = $serializer;
    }
}