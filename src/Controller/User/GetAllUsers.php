<?php
// src/Controller/User/GetAllUsers.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Component\AuthVerification;

class GetAllUsers extends UserController
{
    #[Route(
        '/users',
        name: 'users',
        methods: ['GET']
    )]
    public function __invoke(Request $request, AuthVerification $authVerification): Response
    {
        if(!$authVerification->verify($request->headers->get('Authorization'))){
            return $authVerification->getUnauthorizedResponse();
        }

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