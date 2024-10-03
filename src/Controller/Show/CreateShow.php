<?php
// src/Controller/Show/CreateShow.php
namespace App\Controller\Show;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Show;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreateShow extends ShowController
{
    #[Route(
        '/api/show',
        name: 'create_show',
        methods: ['POST']
    )]
    #[IsGranted('ROLE_ADMIN', statusCode: 403, message: 'Forbidden')]
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payload = $request->getPayload();
        $params = [
            "name" => [
                "value" => $payload->get("name"),
                "type" => "string",
                "nullable" => false
            ],
            "description" => [
                "value" =>  $payload->get("description"),
                "default" => "",
                "type" => "string",
                "nullable" => true
            ],
            "release_date" => [
                "value" => $payload->get("release_date"),
                "type" => "string",
                "nullable" => false
            ],
            "image" => [
                "value" =>  $payload->get("image"),
                "default" => "",
                "type" => "string",
                "nullable" => true
            ],
            "running_time" => [
                "value" =>  $payload->get("running_time"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "budget" => [
                "value" =>  $payload->get("budget"),
                "default" => 0,
                "type" => "integer",
                "nullable" => true
            ],
            "box_office" => [
                "value" => $payload->get("box_office"),
                "default" => 0,
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
                    $value["value"] = $value["default"];
                }
            }

            if(gettype($value["value"]) != $value["type"]){
                return new Response("Parameter `{$key}` is in incorrect type format, `{$value["type"]}` is needed", 400, ['Content-Type', 'application/json']);
            }
            else{
                if($value["type"] === "string"){
                    $value["value"] = preg_replace('/\s+/','', $value["value"]);
                }
            } 
        }

        if($this->showRepository->findOneBy(array("show_name" => $params["name"]["value"]))){
            return new Response("This show already exist");
        }

        $show = new Show();
        $show->setShowName($params["name"]["value"])
            ->setDescription($params["description"]["value"])
            ->setReleaseDate($params["release_date"]["value"])
            ->setImage($params["image"]["value"])
            ->setRunningTime($params["running_time"]["value"])
            ->setBudget($params["budget"]["value"])
            ->setBoxOffice($params["box_office"]["value"]);

        $entityManager->persist($show);
        $entityManager->flush();

        $json = $this->serializer->serialize(["show" => $show], 'json');
        return new Response($json, 200, ['Content-Type', 'application/json']);
    }
}