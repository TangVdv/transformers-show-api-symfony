<?php
// src/Controller/Show/DeleteShow.php
namespace App\Controller\Show;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\AllShowsNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteShow extends ShowController
{
    #[Route(
        '/api/show/{id}',
        name: 'delete_show',
        methods: ['DELETE'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, EntityManagerInterface $entityManager): Response
    {
        $show = $this->showRepository->findOneBy(array("id" => $id));
        
        if($show){
            $entityManager->remove($show);
            $entityManager->flush();

            return new Response("This show is deleted", 200, ['Content-Type', 'application/json']);
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