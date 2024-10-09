<?php
// src/Controller/Bot/BotController.php
namespace App\Controller\Bot;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\BotRepository;

abstract class BotController extends AbstractController
{
    protected BotRepository $botRepository;
    protected SerializerInterface $serializer;

    public function __construct(BotRepository $botRepository, SerializerInterface $serializer)
    {
        $this->botRepository = $botRepository;
        $this->serializer = $serializer;
    }
}