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

    public function verifyCredentials(){

        //variable de retorno, devuelve si el usuario existe o no
        // $result = array(
        //     "valid" => false,
        //     "id" => null
        // );
        $valid = false;
$c=0;
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
                $c++;
                // si encuentra una coincidencia cambia el valor de valid y crea las variables de sesion id y username
                if(password_verify($this->pass, $user['pass'])){
                    $valid = true;
                    $this->id = $user['id'];
                }
            }
        }

        //retorna la variable booleana valid
        return $valid;
    }

}
