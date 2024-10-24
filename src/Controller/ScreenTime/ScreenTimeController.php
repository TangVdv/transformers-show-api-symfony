<?php
// src/Controller/ScreenTime/ScreenTimeController.php
namespace App\Controller\ScreenTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ScreenTimeRepository;

abstract class ScreenTimeController extends AbstractController
{
    protected ScreenTimeRepository $screenTimeRepository;
    protected SerializerInterface $serializer;

    public function __construct(ScreenTimeRepository $screenTimeRepository, SerializerInterface $serializer)
    {
        $this->screenTimeRepository = $screenTimeRepository;
        $this->serializer = $serializer;
    }
}