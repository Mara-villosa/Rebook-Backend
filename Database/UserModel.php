<?php
require_once 'databaseConfig.php';
require_once(dirname(dirname(__FILE__)) . '/Database/DataClasses/User.php');

/**
 * Modelo para realizar operaciones con la tabla users en la base de datos. 
 * Permite operaciones para el registro e inicio de sesión de usuarios. 
 */
class UserModel{
    /**
     * Devuelve un objeto de conexión a la base de datos MySQLi.
     * Devuelve null si no se puede conectar.
     * @param string $server servidor de la base de datos
     * @param string $database nombre de la base de datos
     * @param string $user usuario de conexión
     * @param string $password contraseña de conexión
     * @return mysqli|null
     */
    private function connect(string $server, string $database, string $user, string $password)
    {
        $mysqli = new mysqli($server, $user, $password, $database);

        if ($mysqli->connect_error) {
            return null;
        }

        return $mysqli;
    }

    /**
     * Devuelve los datos de id, nombre e email del usuario si
     * se ha introducido un email y contraseña de login correctos. 
     * Si no, devuelve null.
     * @param string $email email del usuario
     * @param string $password contraseña del usuario
     * @return User|null
     */
    public function logInUser (string $email, string $password){
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM users WHERE EMAIL = ?');
        $query->bind_param('s', $email);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $result = $query_result->fetch_assoc();

        //Si la contraseña no coincide se devuelve null
        if(!password_verify($password, $result['password'])){
            $query_result->free();
            $connection->autocommit(true);
            return null;
        }

        //Se devuelven los datos del usuario
        $user = new User(
            $result['id'],
            $result['name'],
            $result['email'],
        );

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $user;
    }

    /**
     * Registra un usuario en la base de datos indicando su nombre, email y contraseña
     * El email es único, si ya hay uno en la base de datos se cancela el registro.  
     * @param string $name nombre del usuario
     * @param string $email email del usuario
     * @param string $password contraseña del usuario
     * @return bool true si se ha podido insertar
     */
    function signUpUser(string $name, string $email, string $password){
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
       
        $query = $connection->prepare( 'INSERT INTO users (email, name, password) VALUES(?, ?, ?)');

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query->bind_param('sss', $email, $name, $hashedPassword);

        try{
            $query->execute();
        }
        //Se cancela si hay un error de inserción
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }        

        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return true;
    }
}
?>