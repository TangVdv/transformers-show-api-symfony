<?php
// src/Controller/Artefact/CreateArtefact.php
namespace App\Controller\Artefact;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Artefact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\EntityRepository;
use App\Repository\ShowRepository;
use Symfony\Component\Serializer\Serializer;
use App\Normalizer\Artefact\ArtefactNormalizer;
use App\Repository\ScreenTimeRepository;

class CreateArtefact extends ArtefactController
{
    #[Route(
        '/api/artefact',
        name: 'create_artefact',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, EntityRepository $entityRepository, ShowRepository $showRepository, ScreenTimeRepository $screenTimeRepository): Response
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
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true
            ],
            "screen_timeId" => [
                "value" =>  $payload->get("screen_timeId"),
                "type" => "integer",
                "nullable" => true
            ]
        ];

        foreach($params as $key => &$value){
            if($value["value"] === null){
                if(!$value["nullable"]){
                    return new Response("Parameter `{$key}` is missing", 404, ['Content-Type', 'application/json']);
                }
                else{
                    if(array_key_exists("default", $value)){
                        $value["value"] = $value["default"];
                    }
                }
            }
            else{
                if(gettype($value["value"]) != $value["type"]){
                    return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
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

        $screen_time = null;
        if($params["screen_timeId"]["value"] !== null){
            $screen_time = $screenTimeRepository->find($params["screen_timeId"]["value"]);
            if($screen_time === null){
                return new Response("This screen time doesn't exist", 404, ['Content-Type', 'application/json']);
            }
        }

        if($this->artefactRepository->findOneBy(
            array(
                "entity" => $entity, 
                "show" => $show
        ))){
            return new Response("This artefact already exist");
        }

        $artefact = new Artefact();
        $artefact->setImage($params["image"]["value"])
            ->setEntity($entity)
            ->setShow($show);
        if($screen_time !== null){
            $artefact->setScreenTime($screen_time);
        }

        $entityManager->persist($artefact);
        $entityManager->flush();

        $serializer = new Serializer([new ArtefactNormalizer]);
        $data = $serializer->normalize([
            "artefact" => $artefact
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}