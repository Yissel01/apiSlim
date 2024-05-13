<?php 
namespace App\Models;

use App\Models\HTMLVerification as HTMLV;
use App\Models\MetaVerification as MetaV;
use PDO;
class Website extends Model{
    
    public static $table = 'websites';
    private $uwT = 'user_websites'; //nombre de la tabla de usuarios con sus sitios web

    public function getWebsitesTable($userID){
 
        // $sql = 'SELECT ROW_NUMBER() 
        //         OVER (ORDER BY '. self::$table .'.id) AS count,'. 
        //         self::$table .'.url, 
        //         (SELECT EXISTS 
        //             (SELECT 1 
        //             FROM '. HTMLV::$table .' 
        //             WHERE '. HTMLV::$table .'.user_website_id = '. $uwT .'.id)) AS html, 
        //         (SELECT EXISTS 
        //             (SELECT 1 
        //             FROM '. MetaV::$table .' 
        //             WHERE '. MetaV::$table .'.user_website_id = '. $uwT .'.id)) AS meta,
        //         '.$uwT.'.id 
        //         FROM '. self::$table .' 
        //             join '. $uwT .' 
        //             on '. self::$table .'.id = '. $uwT .'.website_id 
        //             and '. $uwT .'.user_id = '. $userID;


        $sql= 'SELECT ROW_NUMBER() 
                OVER (ORDER BY '. self::$table .'.id) AS count,'. 
                self::$table .'.url, ' .$this->uwT.'.id 
                FROM '. self::$table .' 
                    join '. $this->uwT .' 
                    on '. self::$table .'.id = '. $this->uwT .'.website_id 
                    and '. $this->uwT .'.user_id = '. $userID;

        return $this->pdo->query($sql);
        // return $this->pdo->query(" Select ROW_NUMBER() OVER (ORDER BY websites.id) AS count, websites.url , (SELECT EXISTS (SELECT 1 FROM htmlverifications WHERE htmlverifications.user_website_id = user_websites.id)) AS html,(SELECT EXISTS (SELECT 1 FROM metaverifications WHERE metaverifications.user_website_id = user_websites.id)) AS meta FROM websites join user_websites on websites.id = user_websites.website_id and user_websites.user_id = 1");
    }

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

    public function verifyCodes($uwID){
        $code = $this->getCodes($uwID);
        // $html =
    }

    public function getUrlByID($id){
        $sql = 'SELECT '. self::$table .'.url FROM '. self::$table .' Join '. $this->uwT .' on '. self::$table .'.id = '. $this->uwT .'.website_id Where '. $this->uwT .'.id = '. $id ;
        return $this->pdo->query($sql);
    }


}