<?php 
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\Controller;
use App\Middlewares\AuthMiddleware;
use App\Models\Website;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Model;

use function DI\get;

class WebsiteController extends Controller{

    

public function getAll($request,$response,$args){
    $website = new Website(); 
    $query = $website->getAll();
    $response->getBody()->write(json_encode($query->fetchAll()));
    return $response;
    // return $this->formatResponse($response);
}


public function getWebsitesTable($request,$response,$args){
    $website = new Website();
    $userID = $request->getAttribute('id');
    $query = $website->getWebsitesTable($userID);
    return $this->formatRespWithJson($response,['content' => $query->fetchAll()]);
    // return $this->formatRespWithJson($response, ['content' => '']);
    
    // $response->getBody()->write(json_encode($query->fetchAll()));
    // return $this->formatResponse($response);
}

}
