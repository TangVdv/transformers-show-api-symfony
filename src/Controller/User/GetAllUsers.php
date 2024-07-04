<?php
// src/Controller/User/GetAllUsers.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class GetAllUsers extends UserController
{
    #[Route(
        '/api/users',
        name: 'users',
        methods: ['GET']
    )]
    public function __invoke(): Response
    {
        $users = $this->userRepository->findAll();
        
        if($users){
            $json = $this->serializer->serialize($users, 'json');
            return new Response($json, 200, ['Content-Type', 'application/json']);
        }
        else{
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
    }
}