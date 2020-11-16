<?php

namespace App\Controllers;

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;
use \Firebase\JWT\JWT;

class UsuarioController
{

    // public function getAll(Request $request, Response $response, $args)
    // {
    //     $rta = Usuario::get();

    //     $response->getBody()->write(json_encode($rta));
    //     return $response;
    // }

    public function getAllUsers()
    {
        return Usuario::get();
    }

    public function getOne(Request $request, Response $response, $args)
    {
        $rta = Usuario::find($args['id']);

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    //PUNTO UNO OK
    public function addOne(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();
        $user = new Usuario;
        $rta = "";
        $users = $this->getAllUsers();
        $unique = true;

        if (strlen($body["clave"]) > 4) {
            if (count($users) > 0) {
                foreach ($users as $value) {
                    if ($value->email == $body['email'] || $value->nombre == $body['nombre']) {
                        $unique = false;
                        break;
                    }
                }
            }

            if ($unique) {
                $user->email = strtolower($body['email']);
                $user->clave = $body['clave'];
                $user->tipo = $body['tipo'];
                $user->nombre = strtolower($body['nombre']);

                $rta = $user->save();
            } else {
                $rta = "Nombre o Email repetidos... no se puede registrar.";
            }
        } else {
            $rta = "La clave tiene que tener al menos 4 caracteres. No se puede registrar.";
        }
        $response->getBody()->write(json_encode($rta));
        return $response;
    }




    public function login(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();

        $id = isset($body['email']) ? 'email' : (isset($body['nombre']) ? 'nombre' : null);

        if ($id != null) {
            $user = Usuario::where($id, '=', strtolower($body[$id]))->where('clave', '=', $body['clave'])->get()->first();
        }
        if (!$user) {
            $response = new Response();
            $response->getBody()->write("Usuario inexistente");

            return $response->withStatus(401);
        } else {
            $encodeOk = false;
            $payload = array();

            $payload = array(
                "email" => $user->email,
                "tipo" => $user->tipo
            );
            $encodeOk = JWT::encode($payload, "parcial");
            $response = new Response();
            $response->getBody()->write(json_encode($encodeOk));

            return $response->withStatus(200);
        }
    }

    public function update(Request $request, Response $response, $args)
    {
        $user = Usuario::find($args['id']);

        $rta = $user->save();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function delete(Request $request, Response $response, $args)
    {
        $user = Usuario::find($args['id']);

        $rta = $user->delete();
        $response->getBody()->write(json_encode($rta));
        return $response;
    }
}
