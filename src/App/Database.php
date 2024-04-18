<?php
namespace App\App;

use PDO;


class Database
{
    private static $host = 'localhost';
    private static $charset = 'utf8';
    private static $dbname = 'seowebmasdb';
    private static $user = 'root';
    private static $pass = '';
    private static $opt = [
        //Para lanzar excepciones si hay errores de conexion con la BD
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //Para que todos los resultados los envie en un objeto
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];


    public static function connect()
    {
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=" . self::$charset;
        return new PDO($dsn, self::$user, self::$pass, self::$opt);
    }

    public static function create(){
        $pdo = self::connect();
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                pass VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS websites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                url VARCHAR(255) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS user_websites (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                website_id INT,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (website_id) REFERENCES websites(id),
                UNIQUE (user_id, website_id)
            );

            CREATE TABLE IF NOT EXISTS htmlverifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(255) NOT NULL UNIQUE,
                user_website_id INT,
                FOREIGN KEY (user_website_id) REFERENCES websites(id)
            );
            
            CREATE TABLE IF NOT EXISTS metaverifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                tag VARCHAR(255) NOT NULL UNIQUE,
                user_website_id INT,
                FOREIGN KEY (user_website_id) REFERENCES websites(id)
            )' 
        );
        
        $num = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

        if($num == 0){
            
            for ($i = 1; $i <= 20; $i++) {

                // INSERT para la tabla 'users'
                $username = "user" . $i; // Generar el nombre de usuario
                $pass = password_hash("password" . $i, PASSWORD_DEFAULT); // Generar una contraseña encriptada
                $insert = $pdo->prepare("INSERT INTO users (username, pass) VALUES (:username, :pass)");
                $insert->execute([
                    ':username' => $username,
                    ':pass' => $pass
                ]);

                // INSERT para la tabla 'websites' 
                $url = "http://www.example" . $i . ".com";
                $insert = $pdo->prepare("INSERT INTO websites (url) VALUES (:url)");
                $insert->execute([':url' => $url]);
                
                // INSERT para la tabla 'htmlverifications'
                $code = "<html><head><title>Random HTML Code $i</title></head><body></body></html>";
                $insert = $pdo->prepare("INSERT INTO htmlverifications (code, user_website_id) VALUES (:code, :user_website_id)");
                $insert->execute([
                    ':code' => $code,
                    ':user_website_id' => $i
                ]);

                // INSERT para la tabla 'metaverifications' 
                $tag = "<meta name=\"SeoWebMasVerification\" content=\"Random meta tag $i\">";
                $insert = $pdo->prepare("INSERT INTO metaverifications (tag, user_website_id) VALUES (:tag, :user_website_id)");
                $insert->execute([
                    ':tag' => $tag,
                    ':user_website_id' => $i
                ]);

                // INSERT para la tabla 'user_websites'
                $insert = $pdo->prepare("INSERT INTO user_websites (user_id, website_id) VALUES (:user_id, :website_id)");
                
                //Para poner 10 sitios web al usuario con id 1 y 10 al usuario con id 2
                if($i <=10){
                    $insert->execute([
                        ':user_id' => 1,
                        ':website_id' => $i
                    ]);
                }else{
                    $insert->execute([
                        ':user_id' => 2,
                        ':website_id' => $i
                    ]);
                }
            }
        }       
    }
}