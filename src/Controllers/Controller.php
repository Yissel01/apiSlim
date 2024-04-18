<?php 
namespace App\Controllers;


class Controller{

    public function formatResponse($response,$status = 200){
        return $response
        ->withHeader('Content-Type','application/json')
        ->withStatus($status);
    }

    public function respWithJson($response, $data = []){
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function formatRespWithJson($response, $data = [], $status = 200){
        return $this->formatResponse($this->respWithJson($response, $data), $status);
    }
}