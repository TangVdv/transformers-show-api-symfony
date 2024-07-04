<?php
// src/Controller/User/GetUser.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetUser extends UserController
{
    #[Route(
        '/api/user/{id}',
        name: 'user',
        methods: ['GET']
    )]
    public function __invoke(int $id, TokenStorageInterface $tokenStorageInterface, JWTTokenManagerInterface $jwtManager): Response
    {
        $decodedJwtToken = $jwtManager->decode($tokenStorageInterface->getToken());
        
        $user = $this->userRepository->findOneBy(array("email" => $decodedJwtToken["username"], "id" => $id));

        if($user){
            $json = $this->serializer->serialize($user, 'json');
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
        else{
            return new Response(json_encode([
                "Error" => [
                    "code" => 401,
                    "message" => "Unauthorized"
                ]]), 401, ['Content-Type', 'application/json']
            );
        }
    }
}