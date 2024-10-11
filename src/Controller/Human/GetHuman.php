<?php
// src/Controller/Human/GetHuman.php
namespace App\Controller\Human;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Human\HumanNormalizer;
use Symfony\Component\HttpFoundation\Request;

class GetHuman extends HumanController
{
    #[Route(
        '/api/human/{id}',
        name: 'get_human_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getHumanByID(int $id): Response
    {
        $human = $this->humanRepository->findOneWithParams(array("id" => $id));
        return $this->response($human);
    }

    #[Route(
        '/api/human/{name}',
        name: 'get_human_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getHumanByName(string $name, Request $request): Response
    {
        $show = null;
        if($request->query->get('show') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        $human = $this->humanRepository->findOneWithParams(array("name" => $name, "show" => $show));
        return $this->response($human);
    }

    private function response(mixed $human): Response
    {
        if($human){
            $serializer = new Serializer([new HumanNormalizer]);
            $data = $serializer->normalize([
                "human" => $human
            ], "json");
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