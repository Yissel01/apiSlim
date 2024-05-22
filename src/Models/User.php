<?php 
namespace App\Models;

use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class User extends Model{

    private $id;
    private $username; 
    private $pass;
    public static $table = 'users';

    public function __construct($username = null, $pass = null){
        parent::__construct();
        $this->username = $username;
        $this->pass=$pass;
    }

    public function getID(){
        return $this->id;
    }
    
    public function getUsername(){
        return $this->username;
    }

    public function verifyCredentials(){

        //variable de retorno, devuelve si el usuario existe o no y en caso de que exista devuelve tambien el jwt
        $result['valid'] = false;
        $result['jwt'] = null;
        // consulta para obtener todos los usuarios con ese nombre
        $sql = 'SELECT id, pass FROM '. self::$table .' WHERE username = :username';
        $data = $this->pdo->prepare($sql);
        $data->execute([
            ':username' => $this->username
        ]);

        // guarda las tuplas devueltas por la consulta en un arreglo
        $users = $data->fetchAll(PDO::FETCH_ASSOC);

        // si el arreglo no es vacio
        if (!empty($users)) {
            //lo recorre en busca de coincidencias en la contraseña
            foreach ($users as $user) {
                // si encuentra una coincidencia cambia el valor de valid y le da el valor del id al atributo id de la clase
                if(password_verify($this->pass, $user['pass'])){
                    $result['valid'] = true;
                    $result['jwt'] = $this->generateToken($user['id']);
                }
            }
        }
        
        return $result;
    }

     //genera un jwt con tiempo de expiracion de 1 hora
     public function generateToken($id){
        $now = strtotime("now");
        $payload = [
            'exp' => $now + 3600,
            'data' => $id,
        ];
        
        return JWT::encode($payload, Model::$hashKey, 'HS256');
    }

}
