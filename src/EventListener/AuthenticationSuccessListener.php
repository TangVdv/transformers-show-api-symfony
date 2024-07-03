<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Logs;
use App\Entity\User;
use Exception;

class AuthenticationSuccessListener
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private User $user;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager) 
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $this->user = $event->getUser();

        if(!$this->user instanceof User){
            return;
        }

        try{
            $log = new Logs();

            if($request->headers->has("user-agent")){
                $log->setUserAgent($request->headers->get("user-agent"));
            }
            $log->setEndpoint($request->getRequestUri());
            $log->setMethod($request->getMethod());
            $log->setRequestAt(new \DateTimeImmutable());
            $log->setUserId($this->user->getId());

            $this->entityManager->persist($log);
            $this->entityManager->flush();
        }
        catch (Exception $e) {
            throw new Exception($e);
        }
    }
}