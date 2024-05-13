<?php

use App\Middlewares\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api', function(RouteCollectorProxy $group){
    $group->get('/user','App\Controllers\UserController:getAll');
    $group->get('/logout','App\Controllers\UserController:logout');
    $group->get('/find','App\Controllers\WebsiteController:findTag');
    $group->get('/website','App\Controllers\WebsiteController:getWebsitesTable')->add(AuthMiddleware::class);
    $group->get('/website_verification_codes','App\Controllers\WebsiteController:getVerificationCodes');
    $group->get('/check_website','App\Controllers\WebsiteController:checkVerification');
    $group->post('/verify_credencials','App\Controllers\UserController:verifyCredentials');
    $group->post('/a','App\Controllers\UserController:a');
});

