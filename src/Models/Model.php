<?php 
namespace App\Models;

use App\App\Database;


class Model{
    
    protected $pdo; 
    protected static $table;
    public static $hashKey = '8uRhAeH89naYfFXKGOEj';

    public function __construct(){   
        $this->pdo = Database::connect();
    }

    public function getAll(){
        return  $this->pdo->query("SELECT * FROM ".self::$table);
    }

}
