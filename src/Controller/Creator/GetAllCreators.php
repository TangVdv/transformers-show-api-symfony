<?php
// src/Controller/Creator/GetAllCreators.php
namespace App\Controller\Creator;

use App\Normalizer\Creator\CreatorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

class GetAllCreators extends CreatorController
{
    #[Route(
        '/api/creators',
        name: 'get_creators',
        methods: ['GET']
    )]
    public function __invoke(Request $request): Response
    {
        $limit = 10;
        $show = null;
        $category = null;


        if($request->query->get('limit') !== null && !empty($request->query->get('limit'))){
            if(filter_var($request->query->get('limit'), FILTER_VALIDATE_INT)){
                $limit = $request->query->getInt('limit');
            }
            else{
                return new Response("Parameter `limit` is in incorrect type format, `integer` is needed", 400, ['Content-Type', 'application/json']);
            }
        }

        if($request->query->get('show') !== null && !empty($request->query->get('show'))){
            $show = filter_var($request->query->get('show'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        if($request->query->get('category') !== null && !empty($request->query->get('category'))){
            $category = filter_var($request->query->get('category'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }
        
        $creators = $this->creatorRepository->findAllWithParams($limit, $show, $category);

        if($creators){
            $serializer = new Serializer([new CreatorNormalizer]);
            $data = $serializer->normalize([
                "creator_total" => count($creators),
                "limit" => $limit,
                "creators" => $creators
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