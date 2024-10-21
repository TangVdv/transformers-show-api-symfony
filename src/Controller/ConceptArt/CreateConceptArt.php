<?php
// src/Controller/ConceptArt/CreateConceptArt.php
namespace App\Controller\ConceptArt;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ConceptArt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\EntityRepository;
use App\Repository\ShowRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\ConceptArt\CreateUpdateConceptArtNormalizer;

class CreateConceptArt extends ConceptArtController
{
    #[Route(
        '/api/conceptart',
        name: 'create_conceptart',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, EntityRepository $entityRepository, ShowRepository $showRepository): Response
    {
        $payload = $request->getPayload();
        $params = [
            "entityId" => [
                "value" =>  $payload->get("entityId"),
                "type" => "integer",
                "nullable" => false
            ],
            "showId" => [
                "value" => $payload->get("showId"),
                "type" => "integer",
                "nullable" => false
            ],
            "title" => [
                "value" => $payload->get("title"),
                "type" => "string",
                "nullable" => false
            ],
            "image" => [
                "value" => $payload->get("image"),
                "type" => "string",
                "nullable" => false
            ],
            "note" => [
                "value" => $payload->get("note"),
                "type" => "string",
                "nullable" => true,
                "method" => "setNote"
            ],
            "srclink" => [
                "value" =>  $payload->get("srclink"),
                "type" => "string",
                "nullable" => true,
                "method" => "setSrcLink"
            ],
            "date" => [
                "value" =>  $payload->get("date"),
                "type" => "string",
                "nullable" => true,
                "method" => "setDate"
            ]
        ];

        $conceptArt = new ConceptArt();

        foreach($params as $key => &$value){
            if($value["value"] === null){
                if(!$value["nullable"]){
                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                }
            }
            else{
                if(gettype($value["value"]) != $value["type"]){
                    return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
                }
                else{
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $conceptArt->$method($value["value"]);
                    }
                }
            }
        }
        
        $entity = $entityRepository->find($params["entityId"]["value"]);
        if($entity === null){
            return new Response("This entity doesn't exist", 404, ['Content-Type', 'application/json']);
        }
        
        $show = $showRepository->find($params["showId"]["value"]);
        if($show === null){
            return new Response("This show doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        $conceptArt
            ->setTitle($params["title"]["value"])
            ->setImage($params["image"]["value"])
            ->setEntity($entity)
            ->setShow($show);

        $entityManager->persist($conceptArt);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateConceptArtNormalizer]);
        $data = $serializer->normalize([
            "concept_art" => $conceptArt
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}