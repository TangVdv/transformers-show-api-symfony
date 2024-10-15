<?php
// src/Controller/Artist/ArtistController.php
namespace App\Controller\Artist;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ArtistRepository;

abstract class ArtistController extends AbstractController
{
    protected ArtistRepository $artistRepository;
    protected SerializerInterface $serializer;

    public function __construct(ArtistRepository $artistRepository, SerializerInterface $serializer)
    {
        $this->artistRepository = $artistRepository;
        $this->serializer = $serializer;
    }
}