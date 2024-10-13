<?php
// src/Controller/Alt/GetAlt.php
namespace App\Controller\Alt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Alt\AltNormalizer;

class GetAlt extends AltController
{
    #[Route(
        '/api/alt/{id}',
        name: 'get_alt_id',
        methods: ['GET'],
        requirements: ['id' => '\d+']
    )]
    public function getAltByID(int $id): Response
    {
        $alt = $this->altRepository->findOneWithParams(array("id" => $id));
        return $this->response($alt);
    }

    #[Route(
        '/api/alt/{name}',
        name: 'get_alt_name',
        methods: ['GET'],
        requirements: ['name' => '\w+']
    )]
    public function getAltByName(string $name): Response
    {
        $alt = $this->altRepository->findOneWithParams(array("name" => $name));
        return $this->response($alt);
    }

    private function response(mixed $alt): Response
    {
        if($alt){
            $serializer = new Serializer([new AltNormalizer]);
            $data = $serializer->normalize([
                "alt" => $alt
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