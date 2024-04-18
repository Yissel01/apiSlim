<?php 
namespace App\Models;

use App\Models\HTMLVerification as HTMLV;
use App\Models\MetaVerification as MetaV;

class Website extends Model{
    
    public static $table = 'websites';

    public function getWebsitesTable($userID){
        $uwT = 'user_websites'; 

        $sql = 'SELECT ROW_NUMBER() 
                OVER (ORDER BY '. self::$table .'.id) AS count,'. 
                self::$table .'.url, 
                (SELECT EXISTS 
                    (SELECT 1 
                    FROM '. HTMLV::$table .' 
                    WHERE '. HTMLV::$table .'.user_website_id = '. $uwT .'.id)) AS html, 
                (SELECT EXISTS 
                    (SELECT 1 
                    FROM '. MetaV::$table .' 
                    WHERE '. MetaV::$table .'.user_website_id = '. $uwT .'.id)) AS meta 
                FROM '. self::$table .' 
                    join '. $uwT .' 
                    on '. self::$table .'.id = '. $uwT .'.website_id 
                    and '. $uwT .'.user_id = '. $userID;

        return $this->pdo->query($sql);
        // return $this->pdo->query(" Select ROW_NUMBER() OVER (ORDER BY websites.id) AS count, websites.url , (SELECT EXISTS (SELECT 1 FROM htmlverifications WHERE htmlverifications.user_website_id = user_websites.id)) AS html,(SELECT EXISTS (SELECT 1 FROM metaverifications WHERE metaverifications.user_website_id = user_websites.id)) AS meta FROM websites join user_websites on websites.id = user_websites.website_id and user_websites.user_id = 1");
    }

}