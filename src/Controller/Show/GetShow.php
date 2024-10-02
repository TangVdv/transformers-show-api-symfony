<?php
// src/Controller/Show/GetShow.php
namespace App\Controller\Show;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\ShowNormalizer;

class GetShow extends ShowController
{
    #[Route(
        '/api/show/{id}',
        name: 'get_show_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getShowByID(int $id): Response
    {
        $show = $this->showRepository->findOneBy(array("id" => $id));
        
        return $this->response($show);
    }

    #[Route(
        '/api/show/{name}',
        name: 'get_show_name',
        methods: ['GET'],
        requirements: ['name' => '\D+']
    )]
    public function getShowByName(string $name): Response
    {
        $show = $this->showRepository->findByName($name);

        return $this->response($show);
    }

    private function response(mixed $show): Response
    {
        if($show){
            $serializer = new Serializer([new ShowNormalizer]);
            $data = $serializer->normalize(["show" => $show], "json");
            $json = $this->serializer->serialize($data, "json");
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