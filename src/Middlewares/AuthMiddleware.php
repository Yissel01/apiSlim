<?php


namespace App\Middlewares;

use App\App\Database;
use App\Models\Model;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware 
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        //Si la request viene sin la cabecera Authorization corta el flujo 
        //y no pasa la request al metodo del controlador
        if(!$request->getHeader('Authorization')){
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error: ' => 'Sin cabecera']));                
            return $response
            ->withHeader('Content-Type','application/json')
            ->withStatus(401);
        }
        
        //Si la request viene con la cabecera Authorization obtiene el token y lo valida
        $jwt = $this->getToken($request);
        $valid = $this->validateToken($jwt);
        
        //Si el token no es valido corta el flujo y no pasa la request al metodo del controlador
        if(!$valid){
            $response = new Response();
            $response->getBody()->write('Token inválido');
            return $response;
        }
        
        $data = $jwt->data;
        $request = $request->withAttribute('id', $data);
        $response = $handler->handle($request);
        return $response;
    }

    public function getToken(Request $request){
        $auth= $request->getHeader('Authorization');
        $auth = $auth[0];
        $authA = explode(" ", $auth);
        $token = $authA[1];
        return JWT::decode($token, new Key(Model::$hashKey, 'HS256'));
    }

    public function validateToken($jwt){
        $pdo = Database::connect();
        $sql = 'SELECT * FROM '. User::$table. ' WHERE id = :id'; 
        $query = $pdo->prepare($sql);
        $id = $jwt->data;
        $query->execute([
            ':id' => $id
        ]);
        $rows = $query->fetchColumn();
        return $rows;
    }


    public static function logout(){
        session_unset();
        session_destroy();
    }
}