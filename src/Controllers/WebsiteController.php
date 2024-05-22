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

    //Obtiene la lista de sitios web correspondientes al usuario autenticado
    public function getWebsitesTable($request,$response,$args){
        $website = new Website();
        $userID = $request->getAttribute('id');
        $query = $website->getWebsitesTable($userID);
        return $this->formatRespWithJson($response,['content' => $query->fetchAll()]);
    }

    //Obtiene los códigos para la vericacion de propiedad tanto de la etiqueta meta como del archivo html
    public function getVerificationCodes(Request $request, Response $response, $args){
        $website = new Website();
        $userWebsiteID = $request->getHeader('userWebsiteID')[0];
        $code = $website->getCodes($userWebsiteID);
        return $this->formatRespWithJson($response, ['htmlCode' => $code['html'], 'metaCode' => $code['meta']]);
    }

    //Chequea si el sitio web contiene los códigos de verificación de la etiqueta meta o el archivo html
    //y si estos coinciden con los que se le proporcionó al usuario
    public function checkVerificationCodes(Request $request, Response $response, $args){
        $website = new Website();
        $userWebsiteID = $request->getHeader('userWebsiteID')[0];
        $url = $request->getHeader('Website_url')[0];
        $match = $website->verifyCodes($url , $userWebsiteID);
        return $this->formatRespWithJson($response, ['existMeta' => $match['meta'], 'existHTML' => $match['html']]);
    }

}
