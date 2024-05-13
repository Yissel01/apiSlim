<?php
// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\App\Database;
use App\Middlewares\AuthMiddleware;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Response;
use Slim\ResponseEmitter;

require __DIR__ . '/../../vendor/autoload.php';

$app = AppFactory::create();
$server= ServerRequestCreatorFactory::create();
$request = $server->createServerRequestFromGlobals();
Database::create();
// $app->add(AuthMiddleware::class);

// session_start();

// unset($_SESSION['id']);
// solucion de StackOverflow para el error Slim\Exception\HttpNotFoundException
// $app->setBasePath("/SlimTesis/public"); 
//

// $app->get('/', function (Request $request, Response $response, $args) {
    //     $response->getBody()->write("Hello world!");
    //     return $response;
    // });
    
    require __DIR__. '/Routes.php';
    // require __DIR__. '/Configs.php';
    // require __DIR__. '/Dependencies.php';
$app->addBodyParsingMiddleware();   
$response = $app->handle($request);
$emit = new ResponseEmitter();
$emit->emit($response); 

  
// $app->run();