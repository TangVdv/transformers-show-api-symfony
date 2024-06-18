<?php
// src/Controller/Auth/AuthController.php
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use App\Repository\UserAuthRepository;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AuthController extends AbstractController
{
    protected UserRepository $userRepository;
    protected UserAuthRepository $userAuthRepository;
    protected SerializerInterface $serializer;

    public function __construct(UserRepository $userRepository, UserAuthRepository $userAuthRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->userAuthRepository = $userAuthRepository;
        $this->serializer = $serializer;
    }
}