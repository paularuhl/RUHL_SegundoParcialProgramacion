<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Materia;
use App\Models\Usuario;

use App\Models\Alumno_Materia;
use App\Models\Profesor_Materia;

use \Firebase\JWT\JWT;

class MateriaController
{

    public function getAll(Request $request, Response $response, $args)
    {
        $rta = Materia::get();

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


    public function putNote(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $tipo = $body['token']->tipo;
        if ($tipo == 'profesor') {

        $am = Alumno_Materia::where('idMateria', $args['id'])->where('idAlumno', $body['idAlumno']);
        $am->nota = $body['nota'];
        $rta = $am->save();
        } else {
            $rta = "solo un profesor puede poner nota";
        }

        $response->getBody()->write(json_encode($rta));

        return $response;
        
    }

    public function inscripcionAlumno(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $tipo = $body['token']->tipo;
        if ($tipo == 'alumno') {
            $materia = Materia::find($args['id']);
            $cupos = $materia->cupos;
            if ($cupos > 0) {
                $materia->cupos--;
                $materia->save();
            } else {
                $rta = "la materia no tiene mas cupos";
            }

            $user = Usuario::where('email', $body['token']->email)->get()->first();

            $am = new Alumno_Materia;
            $am->idMateria = $materia->idMateria;
            $am->idAlumno = $user->legajo;
            $rta = $am->save();

        } else {
            $rta = "Solo un alumno puede inscribirse a la materia... ud es $tipo";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
}
