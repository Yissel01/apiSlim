<?php 
namespace App\Models;

use App\Models\HTMLVerification as HTMLV;
use App\Models\MetaVerification as MetaV;
use DOMDocument;
use PDO;
class Website extends Model{
    
    public static $table = 'websites';
    private $uwT = 'user_websites'; //nombre de la tabla de usuarios con sus sitios web

    // Obtiene la lista de sitios web correspondientes al usuario autenticado
    public function getWebsitesTable($userID){
 
        $sql= 'SELECT ROW_NUMBER() 
                OVER (ORDER BY '. self::$table .'.id) AS count,'. 
                self::$table .'.url, ' .$this->uwT.'.id 
                FROM '. self::$table .' 
                    join '. $this->uwT .' 
                    on '. self::$table .'.id = '. $this->uwT .'.website_id 
                    and '. $this->uwT .'.user_id = '. $userID;

        return $this->pdo->query($sql);
    }

    // Dado el id de user_website se obtienen los codigos para la verificacion de propiedad tanto meta como html para ese sitio con ese usuario
    public function getCodes($uwID){ //recibe el id de la tabla de usuarios con sus sitios web
        $sql= 'SELECT user_id, website_id FROM '.$this->uwT. ' WHERE id = '. $uwID ;
        $query = $this->pdo->query($sql); 
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $userID = $row['user_id'];
        $websiteID = $row['website_id'];
        $code['meta'] = hash('sha512','seo-meta-verification'.$uwID.$userID.$websiteID.$uwID+$userID+$websiteID);
        $code['html'] = hash('md5','seo-html-verification'.$uwID+$userID+$websiteID.$uwID.$userID.$websiteID);
        return $code;
    }

    //Obtiene los codigos para la verificacion de propiedad de ese sitio para ese usuario mediante el metodo getCodes
    //luego mediante isMetaMatch y isHTMLMatch verifica si estos codigos coinciden con los del sitio web a verificar en caso de que existan
    //devuelve dos valores booleanos en un array que definen si coinciden o no los valores de verificacion
    public function verifyCodes($url ,$uwID){
        $code = $this->getCodes($uwID);
        $match['meta'] = $this->isMetaMatch($url , $code['meta']);
        $match['html'] = $this->isHTMLMatch($url , $code['html']);
        return $match;
    }

    //obtiene el contenido de la pagina que este en la variable url
    //revisa las etiquetas meta de dicha pagina y se queda solo con las que tienen name = seowebmas-verification
    //ve si el content de alguna de estas etiquetas coincide con el codigo de verificacion correspondiente 
    public function isMetaMatch($url, $metaContent){
        $match = false;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);
    
        if ($html !== false && !empty($html)) {
            
            $doc = new DOMDocument();
            
            @$doc->loadHTML($html);
            $metaTags = $doc->getElementsByTagName('meta');
    
            foreach ($metaTags as $meta) {
                if ($meta->getAttribute('name') == 'seowebmas-verification' && $meta->getAttribute('content') == $metaContent) {
                    $match = true;
                    break;
                }
            }
        }
    
        return $match;
    }

    //a partir de la url dada accede a url/nombre_del_archivo_dado.html en caso de que exista y verifica si su contenido es valido
    public function isHTMLMatch($url, $htmlContent){
        $match = false;
        $toCompare = 'seowebmas-verification: seowebmas'. $htmlContent;
        // Inicia cURL con la URL proporcionada.
        $ch = curl_init($url.'/seowebmas'.$htmlContent.'.html');
    
        // Establece la opción de cURL para devolver el resultado como una cadena.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecuta la sesión de cURL y almacena el resultado en $html.
        $html = curl_exec($ch);
     
        // Verifica si ocurrió un error durante la ejecución de cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            // Manejar el error según sea necesario
            curl_close($ch);
            return false;
        }
     
        // Cierra la sesión de cURL.
        curl_close($ch);
     
        // Verifica si se obtuvo el contenido HTML.
        if ($html !== false && !empty($html)) {
            // Se crea una nueva instancia de DOMDocument.
            $doc = new DOMDocument();
            // Carga el HTML obtenido en el objeto DOMDocument.
            @$doc->loadHTML($html);
     
            // Obtiene el elemento body del documento HTML.
            $body = $doc->getElementsByTagName('body')->item(0);
             
            // Extrae el contenido del body.
            $contenidoBody = $body->nodeValue ?? ' ';
            
            //Compara si el contenido del body obtenido coincide con el deseado
            if($contenidoBody === $toCompare){
                $match = true;
            }
         } 
    
        return $match;
    }

}