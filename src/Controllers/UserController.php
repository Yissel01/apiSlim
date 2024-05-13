<?php 
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\Controller;
use App\Middlewares\AuthMiddleware;
use App\Models\Model;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


use function DI\get;

class UserController extends Controller{
    
    public function getAll($request,$response,$args){
        $user = new User(); 
        $query = $user->getAll();
        $response->getBody()->write(json_encode($query->fetchAll()));
        return $this->formatResponse($response);
    }

    public function verifyCredentials(Request $request,Response $response,$args){
        $data = $request->getParsedBody();
        $user = new User($data['username'],$data['pass']); 
        $valid = $user->verifyCredentials();
        if($valid){
            $jwt = $this->generateToken($user->getID());
            return $this->formatRespWithJson($response, ['valid' => $valid, 'token' => $jwt] );
        }
        return $this->formatRespWithJson($response, ['valid' => $valid]);
    }

     //genera un jwt con tiempo de expiracion de 1 hora
     public function generateToken($id){
        $now = strtotime("now");
        $payload = [
            'exp' => $now + 3600,
            'data' => $id,
        ];
        
        return JWT::encode($payload, Model::$hashKey, 'HS256');
    }

    //actualmente en desuso porque se cierra la sesion desde el front borrando el token
    public function logout(Request $request,Response $response,$args){
        AuthMiddleware::logout();
        return $this->formatRespWithJson($response, ['results' => 'Sesión cerrada con éxito']);
    }

    public function a (Request $request,Response $response,$args){
        // consultas sql
        // $response->getBody()->write("Hello world!");
        
        // $uri = $request->getUri();
        // echo 'getUri: '.$uri;
        
        // $d= $request->getQueryParams();
        
        
        // $2y$10$pW8fbYlwniEnZhXbZw.d3edEUFUANEEOzjWXJasEfU5dgSQXrZhJG
        // $2y$10$nmbDZYl0bAPl/DJKC240OuyPMFJk.PlUTs1M/vIo0zXHFwx7.rKsW

        // $nombreParametro = $request->$queryParams['a'];
        // $uriss[2] = $uris->getQueryParams();
        // echo 'a: '.$d['a'];
        // var_dump($uri);
        // $response->getBody()->write(json_encode($uri));
        // $uri = '1';
        // $data = $request->getParsedBody();
        // $d= $request->getQueryParams();
        // // $nombreParametro = $request->$queryParams['a'];
        // if($_SERVER['REQUEST_METHOD'] == 'POST'){
            //     if(isset($_REQUEST)){
                //         $a = [];
                //         foreach($_REQUEST as $params => $values){
                    //             array_push($a, $params.$values);
                    //         }
                    //         $response->getBody()->write(json_encode($d));
        //     }else{
        //         $response->getBody()->write(json_encode('no existe'));
        //     }
    
        //     return $this->formatResponse($response);
        // }
        // $response->getBody()->write("Hello world!");
    
        // $uri = $request->getMethod();
        // $a = $uri;
        // echo $uri;
        // return $response->withHeader('Content-type', 'application/json');
        // return $response->withJson(['user' => 'hols']);
        // return $response;
        // $username = $data['username'];
        // $pass = $data['pass'];
        // $data = $request->getParsedBody();
        // $response->getBody()->write(json_encode($data['username']));
        // return $this->formatResponse($response);
    }

   
}