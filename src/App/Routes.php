<?php

use App\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function(RouteCollectorProxy $group){
    $group->get('/user','App\Controllers\UserController:getAll');
    $group->get('/website','App\Controllers\WebsiteController:getWebsitesTable')->add(AuthMiddleware::class);
    $group->get('/logout','App\Controllers\UserController:logout');
    $group->post('/verify','App\Controllers\UserController:verifyCredentials');
    $group->post('/a','App\Controllers\UserController:a');
});

