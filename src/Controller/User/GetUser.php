<?php
// src/Controller/User/GetUser.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Component\AuthVerification;

class GetUser extends UserController
{
    #[Route(
        '/user/{id}',
        name: 'user',
        methods: ['GET']
    )]
    public function __invoke(int $id, Request $request, AuthVerification $authVerification): Response
    {
        if(!$authVerification->verify($request->headers->get('Authorization'))){
            return $authVerification->getUnauthorizedResponse();
        }

        $user = $this->userRepository->find($id);

        if($user){
            $json = $this->serializer->serialize($user, 'json');
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