<?php
// src/Controller/Auth/Login.php
namespace App\Controller\Auth;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class Login extends AuthController
{
    #[Route(
        '/auth/login',
        name: 'login',
        methods: ['POST']
    )]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $params = [
            "email" => preg_replace('/\s+/','', $request->get("email")),
            "password" => preg_replace('/\s+/','', $request->get("password")),
        ];

        foreach($params as $key => $value){
            if($value == null){
                return new Response("Parameter `$key` is missing", 404, ['Content-Type', 'application/json']);
            }
        }

        $user = $this->userRepository->findOneBy(array(
            "email" => $params["email"]
        ));

        if(!$user){
            return new Response("This email doesn't exist", 404);
        }

        if(!$passwordHasher->isPasswordValid($user, $params["password"])){
            return new Response("Password invalid");
        }

        $auth = $this->userAuthRepository->findOneBy(array("User_id" => $user->getId()));
        
        $json = $this->serializer->serialize($auth, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}