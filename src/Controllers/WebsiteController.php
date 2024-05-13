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
use DOMDocument;

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

public function findMeta($url, $metaContent){
    $exist = false;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html !== false) {
        
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $metaTags = $doc->getElementsByTagName('meta');

        foreach ($metaTags as $meta) {
            if ($meta->getAttribute('name') == 'seowebmas-verification' && $meta->getAttribute('content') == $metaContent) {
                $exist = true;
                break;
            }
        }
    }

    return $exist;
}

public function findHTML($url, $htmlContent){
     // Iniciamos cURL con la URL proporcionada.
     $ch = curl_init($url.'/seowebmas'.$htmlContent.'.html');

     // Establecemos la opción de cURL para devolver el resultado como una cadena.
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
     // Ejecutamos la sesión de cURL y almacenamos el resultado en $html.
     $html = curl_exec($ch);
 
     // Verificar si ocurrió un error durante la ejecución de cURL
     if (curl_errno($ch)) {
         $error_msg = curl_error($ch);
         // Manejar el error según sea necesario
         curl_close($ch);
         return 'Error: ' . $error_msg;
     }
 
     // Cerramos la sesión de cURL.
     curl_close($ch);
 
     // Verificamos si obtuvimos el contenido HTML.
     if ($html !== false) {
         // Creamos una nueva instancia de DOMDocument.
         $doc = new DOMDocument();
         // Cargamos el HTML obtenido en el objeto DOMDocument.
         @$doc->loadHTML($html);
 
         // Obtenemos el elemento body del documento HTML.
         $body = $doc->getElementsByTagName('body')->item(0);
 
         // Extraemos el contenido del body.
         $contenidoBody = $doc->saveHTML($body);
 
         // Devolvemos el contenido del body.
         return'Contenido del body del html: '.$contenidoBody;
     } else {
         return 'No se pudo obtener el contenido.';
     }
}

public function getVerificationCodes(Request $request, Response $response, $args){
    $website = new Website();
    $userWebsiteID = $request->getHeader('userWebsiteID')[0];
    $code = $website->getCodes($userWebsiteID);
    return $this->formatRespWithJson($response, ['htmlCode' => $code['html'], 'metaCode' => $code['meta']]);
}

public function checkVerification(Request $request, Response $response, $args){
    $website = new Website();
    $userWebsiteID = $request->getHeader('userWebsiteID')[0];
    $url = $request->getHeader('Website_url')[0];
    $code = $website->getCodes($userWebsiteID);
    // $url = $website->getUrlByID($userWebsiteID);
    $existMeta = $this->findMeta($url, $code['meta']);
    $existHTML = $this->findHTML($url, $code['html']);
    return $this->formatRespWithJson($response, ['existMeta' => $existMeta, 'existHTML' => $existHTML]);
}



}
