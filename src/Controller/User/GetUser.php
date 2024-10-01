<?php
// src/Controller/User/GetUser.php
namespace App\Controller\User;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetUser extends UserController
{
    #[Route(
        '/api/user/{id}',
        name: 'user',
        methods: ['GET']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id): Response
    {
        $user = $this->userRepository->findOneBy(array("id" => $id));

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