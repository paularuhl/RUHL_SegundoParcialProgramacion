<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Config\Database;
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UsuarioController;
use App\Controllers\MateriaController;

use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;

require __DIR__ . '/../vendor/autoload.php'; // se encarga de incluir todas las dependencias


$app = AppFactory::create();
$app->setBasePath("/RUHL_2DOPARCIALPROG/public");
new Database;

$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('', UsuarioController::class . ":addOne");

})->add(new JsonMiddleware);

$app->group('/login', function (RouteCollectorProxy $group) {

    $group->post('', UsuarioController::class . ":login");

})->add(new JsonMiddleware);

//PUNTO 3 Y 7
$app->group('/materia', function (RouteCollectorProxy $group) {

    $group->post('', MateriaController::class . ":addOne")->add(new AuthMiddleware);

    $group->get('', MateriaController::class . ":getAll")->add(new AuthMiddleware);

})->add(new JsonMiddleware);



$app->group('/inscripcion', function (RouteCollectorProxy $group) {

    $group->get('/{id}', MateriaController::class . ":")->add(new AuthMiddleware);

    //PUNTO 4
    $group->post('/{id}', MateriaController::class . ":inscripcionAlumno")->add(new AuthMiddleware);

})->add(new JsonMiddleware);


$app->group('/notas', function (RouteCollectorProxy $group) {

    $group->put('/{id}', MateriaController::class . ":putNote")->add(new AuthMiddleware);

})->add(new JsonMiddleware);



$app->run();
