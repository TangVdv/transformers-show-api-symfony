<?php
// src/Controller/Auth/Signup.php
namespace App\Controller\Auth;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\UserAuth;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class Signup extends AuthController
{
    #[Route(
        '/auth/signup',
        name: 'signup',
        methods: ['POST']
    )]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $params = [
            "username" => preg_replace('/\s+/','', $request->get("username")),
            "email" => preg_replace('/\s+/','', $request->get("email")),
            "password" => preg_replace('/\s+/','', $request->get("password")),
        ];

        foreach($params as $key => $value){
            if($value == null){
                return new Response("Parameter `$key` is missing", 404, ['Content-Type', 'application/json']);
            }
        }

        if($this->userRepository->findOneBy(array("email" => $params["email"]))){
            return new Response("This user already exist");
        }

        $factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);

        $passwordHasher = $factory->getPasswordHasher('common');

        $uuid = Uuid::v7();

        $user = new User();
        $user->setUuid($uuid);
        $user->setUsername($params["username"]);
        $user->setEmail($params["email"]);
        $user->setPassword($passwordHasher->hash($params["password"]));

        $entityManager->persist($user);
        $entityManager->flush();

        $auth = new UserAuth();
        $auth->setAccessToken($this->userAuthRepository->generateToken());
        $auth->setUserId($user->getId());

        $entityManager->persist($auth);
        $entityManager->flush();

        $json = $this->serializer->serialize($user, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}