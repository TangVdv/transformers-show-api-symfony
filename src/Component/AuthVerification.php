<?php

namespace App\Component;

use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserAuthRepository;
use App\Entity\UserAuth;

class AuthVerification
{
    private UserAuthRepository $userAuthRepository;

    public function __construct(UserAuthRepository $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function verify(?String $auth): bool
    {
        if(isset($auth)) {
            $token = $this->getTokenFromAuthorization($auth);
            if ($token) {
                $userAuth = $this->userAuthRepository->findOneBy(array("access_token" => $token));

                if($userAuth){
                    return !$this->isTokenExpired($userAuth);
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    public function getTokenFromAuthorization(String $auth): String
    {
        preg_match('/Bearer\s(\S+)/', $auth, $matches);
        return $matches[1];
    }

    public function isTokenExpired(UserAuth $userAuth): bool
    {
        return ($userAuth->getIssuedAt() + $userAuth->getExpiresIn()) < time();
    }

    public function getUnauthorizedResponse(): Response
    {
        return new Response(json_encode([
            "Error" => [
                "code" => "401",
                "message" => "Unauthorized"
            ]
        ]), 401, ['Content-Type', 'application/json']);
    }
}