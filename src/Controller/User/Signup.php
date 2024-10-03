<?php
// src/Controller/User/Signup.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\EntityManagerInterface;

class Signup extends UserController
{
    #[Route(
        '/auth/signup',
        name: 'signup',
        methods: ['POST']
    )]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getPayload();

        $params = [
            "username" => preg_replace('/\s+/','', $payload->get("username")),
            "email" => preg_replace('/\s+/','', $payload->get("email")),
            "password" => preg_replace('/\s+/','', $payload->get("password")),
        ];

        foreach($params as $key => $value){
            if($value == null){
                return new Response("Parameter `$key` is missing", 404, ['Content-Type', 'application/json']);
            }
        }

        if($this->userRepository->findOneBy(array("email" => $params["email"]))){
            return new Response("This user already exist");
        }

        $uuid = Uuid::v7();

        $user = new User();
        $user->setUuid($uuid)
            ->setUsername($params["username"])
            ->setEmail($params["email"])
            ->setPlainPassword($params["password"]);

        $entityManager->persist($user);
        $entityManager->flush();

        $json = $this->serializer->serialize($user, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}