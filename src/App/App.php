<?php
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
    
require __DIR__. '/Routes.php';

$app->addBodyParsingMiddleware();   
$response = $app->handle($request);
$emit = new ResponseEmitter();
$emit->emit($response); 

  
// $app->run();