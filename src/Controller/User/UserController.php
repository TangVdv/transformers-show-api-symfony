<?php
// src/Controller/User/UserController.php
namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;

abstract class UserController extends AbstractController
{
    protected UserRepository $userRepository;
    protected SerializerInterface $serializer;

    public function __construct(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }
}