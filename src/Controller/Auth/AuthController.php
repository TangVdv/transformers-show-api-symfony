<?php
// src/Controller/Auth/AuthController.php
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AuthController extends AbstractController
{
    protected UserRepository $userRepository;
    protected SerializerInterface $serializer;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }
}