<?php
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/UserDTO.php');
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
     * @return UserDTO|null
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
        $user = new UserDTO(
            $result['id'],
            $result['name'],
            $result['email'],
            $result['lastname'],
            $result['id_document'],
            $result['birthday'],
            $result['city'],
            $result['address'],
            $result['postal_code'],
            $result['phone']
        );

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $user;
    }

    /**
     * Registra un usuario en la base de datos
     * El email es único, si ya hay uno en la base de datos se cancela el registro.  
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $lastname
     * @param string $id_document
     * @param string $birthday
     * @param string $city
     * @param string $address
     * @param string $postal_code
     * @param string $phone
     * @param string|null $card_name
     * @param string|null $card_number
     * @param string|null $cvv
     * @return bool true si se ha podido insertar
     */
    function signUpUser(
        string $name, 
        string $email, 
        string $password, 
        string $lastname, 
        string $id_document, 
        string $birthday, 
        string $city, 
        string $address, 
        string $postal_code, 
        string $phone, 
        string | null $card_name = null, 
        string | null $card_number = null, 
        string | null $cvv = null)
        {
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
       
        $query = $connection->prepare( 'INSERT INTO users (email, name, password, lastname, id_document, birthday, city, address, postal_code, phone, card_name, card_number, cvv) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $hashedCardName = null;
        $hashedCardNumber = null;
        $hashedCVV = null;
        if(isset($card_name) && isset($card_number) && isset($cvv)){
            $hashedCardName = password_hash($card_name, PASSWORD_DEFAULT);
            $hashedCardNumber = password_hash($card_number, PASSWORD_DEFAULT);
            $hashedCVV = password_hash($cvv, PASSWORD_DEFAULT);
        }

        //$date = DateTime::createFromFormat('Y-m-d', $birthday);

        $query->bind_param('sssssssssssss', $email, $name, $hashedPassword, $lastname, $id_document, $birthday, $city, $address, $postal_code, $phone, $hashedCardName, $hashedCardNumber, $hashedCVV);

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

    /**
     * Actualiza el usuario en la base de datos con los campos pasados que no sean null
     * @param int $userId
     * @param string|null $name
     * @param string|null $email
     * @param string|null $oldPassword
     * @param string|null $newPassword
     * @param string|null $lastname
     * @param string|null $id_document
     * @param string|null $birthday
     * @param string|null $city
     * @param string|null $address
     * @param string|null $postal_code
     * @param string|null $phone
     * @param string|null $card_name
     * @param string|null $card_number
     * @param string|null $cvv
     * @return bool true si se han actualizado con éxito los datos
     */
    function updateUser(
        int $userId, 
        string | null $name = null, 
        string | null $email = null, 
        string | null $oldPassword = null, 
        string | null $newPassword = null,
        string | null $lastname = null, 
        string | null $id_document = null, 
        string | null $birthday = null, 
        string | null $city = null, 
        string | null $address = null, 
        string | null $postal_code = null, 
        string | null $phone = null, 
        string | null $card_name = null, 
        string | null $card_number = null, 
        string | null $cvv = null): bool
    {
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
        $success = true;

        if(isset($name)){
            if(!$this->updateField($connection, $userId, 'name', $name)) $success = false;
        }
        if(isset($email)){
            if(!$this->updateField($connection, $userId, 'email', $email)) $success = false;
        }
        if(isset($lastname)){
            if(!$this->updateField($connection, $userId, 'lastname', $lastname)) $success = false;
        }
        if(isset($id_document)){
            if(!$this->updateField($connection, $userId, 'id_document', $id_document)) $success = false;
        }
        if(isset($birthday)){
            if(!$this->updateField($connection, $userId, 'birthday', $birthday)) $success = false;;
        }
        if(isset($city)){
            if(!$this->updateField($connection, $userId, 'city', $city)) $success = false;
        }
        if(isset($address)){
            if(!$this->updateField($connection, $userId, 'address', $address)) $success = false;
        }
        if(isset($postal_code)){
            if(!$this->updateField($connection, $userId, 'postal_code', $postal_code)) $success = false;
        }
        if(isset($phone)){
            if(!$this->updateField($connection, $userId, 'phone', $phone)) $success = false;
        }
        if(isset($card_name)){
            if(!$this->updateField($connection, $userId, 'card_name', $card_name)) $success = false;
        }
        if(isset($card_number)){
            if(!$this->updateField($connection, $userId, 'card_number', $card_number)) $success = false;
        }
        if(isset($cvv)){
            if(!$this->updateField($connection, $userId, 'cvv', $cvv)) $success = false;;
        }

        //Actualización de contraseña
        if(isset($oldPassword) && isset($newPassword)){
            $query = $connection->prepare('SELECT * FROM users WHERE ID = ?');
            $query->bind_param('i', $userId);
            $query->execute();
            $query_result = $query->get_result();
            $result = $query_result->fetch_assoc();

            //Si la contraseña antigua no coincide se cancela la actualización
            if(password_verify($oldPassword, $result['password'])){
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->updateField($connection, $userId, 'password', $hashedPassword);
            }
            else {
                $query_result->free();
                $connection->autocommit(true);
                $success = false;
            } 
        }

        if(!$success){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        $connection->commit();
        $connection->autocommit(true);
        $connection->close();
        return true;
    }

    private function updateField($connection, $userId, $fieldName, $field): bool{
        return $connection->query('UPDATE users set ' . $fieldName . ' = ' . '\''. $field . '\'' .' WHERE id = ' . $userId);
    }
}
?>