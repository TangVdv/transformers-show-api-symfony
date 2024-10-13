<?php
// src/Controller/VoiceActor/CreateVoiceActor.php
namespace App\Controller\VoiceActor;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Serializer;
use App\Repository\NationalityRepository;
use App\Entity\VoiceActor;
use App\Normalizer\VoiceActor\CreateUpdateVoiceActorNormalizer;

class CreateVoiceActor extends VoiceActorController
{
    #[Route(
        '/api/voiceactor',
        name: 'create_voiceactor',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager, NationalityRepository $nationalityRepository): Response
    {
        $payload = $request->getPayload();
        $params = [
            "first_name" => [
                "value" =>  $payload->get("first_name"),
                "type" => "string",
                "nullable" => false
            ],
            "last_name" => [
                "value" => $payload->get("last_name"),
                "type" => "string",
                "nullable" => false
            ],
            "nationalityId" => [
                "value" => $payload->get("nationalityId"),
                "type" => "integer",
                "nullable" => false
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
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
        
        $nationality = $nationalityRepository->find($params["nationalityId"]["value"]);
        if(!$nationality){
            return new Response("This nationality doesn't exist", 404, ['Content-Type', 'application/json']);
        }

        if($this->voiceactorRepository->findOneBy(
            array(
                "voiceactor_firstname" => $params["first_name"]["value"], 
                "voiceactor_lastname" => $params["last_name"]["value"]
        ))){
            return new Response("This voice actor already exist", 400, ['Content-Type', 'application/json']);
        }

        $voice_actor = new VoiceActor();
        $voice_actor
            ->setVoiceActorFirstname($params["first_name"]["value"])
            ->setVoiceActorLastname($params["last_name"]["value"])
            ->setNationality($nationality)
            ->setImage($params["image"]["value"]);
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