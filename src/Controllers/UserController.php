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
    
    public function verifyCredentials(Request $request,Response $response,$args){
        $data = $request->getParsedBody();
        $user = new User($data['username'],$data['pass']); 
        $result = $user->verifyCredentials();
        
        if($result['valid']){
            return $this->formatRespWithJson($response, ['valid' => $result['valid'], 'token' => $result['jwt']] );
        }

        return $this->formatRespWithJson($response, ['valid' => $result['valid']]);
    }

    //actualmente en desuso porque se cierra la sesion desde el front borrando el token
    public function logout(Request $request,Response $response,$args){
        AuthMiddleware::logout();
        return $this->formatRespWithJson($response, ['results' => 'Sesión cerrada con éxito']);
    }
 
}