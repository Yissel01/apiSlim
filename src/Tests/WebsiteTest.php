<?php

use App\Models\Website;
use PHPUnit\Framework\TestCase;

class WebsiteTest extends TestCase{
    private $website;

    public function setUP():void{
        $this->website = new Website();
    }

    public function testGetWebsitesTable(){
        $userID = 1; // Simula el ID del usuario para la prueba
        $result = $this->website->getWebsitesTable($userID); // Ejecuta el método getWebsitesTable        
        $this->assertInstanceOf(PDOStatement::class, $result); // Verifica que el resultado es una instancia de PDOStatement
    }

    public function testGetCodes(){
        $uwID = 1; // Simula el ID de la tabla user_websites para la prueba

        $codes = $this->website->getCodes($uwID);

        $this->assertIsArray($codes); // Verifica que se devuelve un array

        // Verifica que el array contiene las claves 'meta' y 'html'
        $this->assertArrayHasKey('meta', $codes);
        $this->assertArrayHasKey('html', $codes);

        // Verifica que los códigos son cadenas no vacías
        $this->assertNotEmpty($codes['meta']);
        $this->assertNotEmpty($codes['html']);
    }

    public function testVerifyCodes()
    {
        // Simula el ID de la tabla user_websites y la URL para la prueba
        $uwID = 1;
        $url = 'http://example.com';

        $match = $this->website->verifyCodes($url, $uwID);

        $this->assertIsArray($match);
        $this->assertArrayHasKey('meta', $match);
        $this->assertArrayHasKey('html', $match);

        // Verifica que los valores son booleanos
        $this->assertIsBool($match['meta']);
        $this->assertIsBool($match['html']);
    }

    public function testIsMetaMatch()
    {
         // Simula la URL y el contenido meta para la prueba
         $url = 'http://example.com';
         $metaContent = 'someMetaContent';
 
         $result = $this->website->isMetaMatch($url, $metaContent);
 
         $this->assertIsBool($result);
    }

    public function testIsHTMLMatch()
    {
        // Simula la URL y el contenido HTML para la prueba
        $url = 'http://example.com';
        $htmlContent = 'someHtmlContent';

        $result = $this->website->isHTMLMatch($url, $htmlContent);

        $this->assertIsBool($result);
    }

}

?>