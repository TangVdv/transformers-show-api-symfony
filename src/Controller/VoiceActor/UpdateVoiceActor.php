<?php
// src/Controller/VoiceActor/UpdateVoiceActor.php
namespace App\Controller\VoiceActor;

use App\Normalizer\VoiceActor\CreateUpdateVoiceActorNormalizer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\NationalityRepository;

class UpdateVoiceActor extends VoiceActorController
{
    #[Route(
        '/api/voiceactor/{id}',
        name: 'update_voiceactor',
        methods: ['PUT'],
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(int $id, Request $request, EntityManagerInterface $entityManager, NationalityRepository $nationalityRepository): Response
    {
        $voice_actor = $this->voiceactorRepository->findOneWithParams(array("id" => $id));

        if(!$voice_actor){
            return new Response(json_encode([
                "Error" => [
                    "code" => 404,
                    "message" => "Couldn't find any data."
                ]]), 404, ['Content-Type', 'application/json']
            );
        }
        $payload = $request->getPayload();
        $params = [
            "first_name" => [
                "value" =>  $payload->get("first_name"),
                "default" => $voice_actor->getVoiceActorFirstname(),
                "type" => "string",
                "nullable" => true
            ],
            "last_name" => [
                "value" => $payload->get("last_name"),
                "default" => $voice_actor->getVoiceActorLastname(),
                "type" => "string",
                "nullable" => true
            ],
            "nationalityId" => [
                "value" => $payload->get("nationalityId"),
                "type" => "integer",
                "nullable" => true
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true,
                "method" => "setImage"
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
                else{
                    if(array_key_exists("method", $value)){
                        $method = $value["method"];
                        $voice_actor->$method($value["value"]);
                    }
                }
            }
        }
        
        if($params["first_name"]["value"] !== $params["first_name"]["default"] || $params["last_name"]["value"] !== $params["last_name"]["default"]){
            if($this->voiceactorRepository->findOneBy(array("actor_firstname" => $params["first_name"]["value"], "actor_lastname" => $params["last_name"]["value"]))){
                return new Response("An actor with this name already exist", 400, ['Content-Type', 'application/json']);
            }
            $voice_actor->setVoiceActorFirstname($params["first_name"]["value"])
                ->setVoiceActorLastname($params["last_name"]["value"]);
        }

        if($params["nationalityId"]["value"] !== null){
            $nationality = $nationalityRepository->find($params["nationalityId"]["value"]);
            if($nationality === null){
                return new Response("This nationality doesn't exist", 404, ['Content-Type', 'application/json']);
            }
            $voice_actor->setNationality($nationality);
        }

        $entityManager->persist($voice_actor);
        $entityManager->flush();

        $serializer = new Serializer([new CreateUpdateVoiceActorNormalizer]);
        $data = $serializer->normalize([
            "voiceactor" => $voice_actor
        ], "json");
        $json = $this->serializer->serialize($data, 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}