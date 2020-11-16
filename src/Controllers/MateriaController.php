<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;
use \Firebase\JWT\JWT;

class MateriaController
{

    public function getAll(Request $request, Response $response, $args)
    {
        $rta = Materia::get();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getOne(Request $request, Response $response, $args)
    {
        $rta = Materia::find($args['id']);

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function addOne(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $tipo = $body['token']->tipo;
        if ($tipo == 'admin') {
            $materia = new Materia;

            if ($body['cuatrimestre'] < 5 && $body['cuatrimestre'] > 0) {
                $materia->cuatrimestre = $body['cuatrimestre'];
                $materia->nombre = $body['materia'];
                $materia->cupos = $body['cupos'];
                $rta = $materia->save();
            } else {
                $rta = "Error, Cuatrimestre no puede ser menor a 1 ni mayor a 4";
            }
        } else {
            $rta = "El usuario $tipo no tiene acceso a agregar materias...";
            $response->withStatus(401);
        }

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function update(Request $request, Response $response, $args)
    {
        $user = Materia::find($args['id']);

        $rta = $user->save();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getAllNotes(Request $request, Response $response, $args)
    {
        $user = Materia::find($args['id']);

        $rta = $user->delete();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
}
