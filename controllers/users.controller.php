<?php
require_once(ROOT . '/database/models/UserModel.php');
class UsersController{
    /**
     * Endpoint /signup
     * Inserta un usuario dado su name, email, password, lastname, id_document, birthday, city, address, postal_code y phone.
     * Opcionalmente, puede recibir card name, card number y cvv para registrar la tarjeta del usuario en la base de datos. 
     * 200OK si puede hacerlo, 400 si no
     */
    public static function signup(){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['name'])) returnHTTPError('Name not provided', 400);
        if(!isset($data['lastname'])) returnHTTPError('Lastname not provided', 400);
        if(!isset($data['email'])) returnHTTPError('Email not provided', 400);
        if(!isset($data['password'])) returnHTTPError('Password not provided', 400);
        if(!isset($data['id_document'])) returnHTTPError('ID Document not provided', 400);
        if(!isset($data['birthday'])) returnHTTPError('Birthday not provided', 400);
        if(!isset($data['city'])) returnHTTPError('City not provided', 400);
        if(!isset($data['address'])) returnHTTPError('Address not provided', 400);
        if(!isset($data['postal_code'])) returnHTTPError('Postal Code not provided', 400);
        if(!isset($data['phone'])) returnHTTPError('Phone not provided', 400);

        //Campos opcionales de datos de la tarjeta
        $card_name = null;
        $card_number = null;
        $cvv = null;
        if(isset($data['card_name']) && isset($data['card_number']) && isset($data['cvv'])){
            $card_name = $data['card_name'];
            $card_number = $data['card_number'];
            $cvv = $data['cvv'];

            //Validación de datos de la tarjeta
            if(strlen($card_number) !== 16) returnHTTPError('Invalid card number', 400);
            if(strlen($cvv) !== 3) returnHTTPError('Invalid CVV', 400);
        } 

        $model = new UserModel();

        $inserted = $model->signUpUser(
            $data['name'], 
            $data['email'], 
            $data['password'],
            $data['lastname'],
            $data['id_document'], 
            $data['birthday'],
            $data['city'],
            $data['address'],
            $data['postal_code'],
            $data['phone'],
            $card_name,
            $card_number,
            $cvv);

        if($inserted){
            //Se devuelve 201 Created
            http_response_code(201);
            $response = array('message' => "User Created");
            echo json_encode($response);
            exit;
        }
        else{
            returnHTTPError('Invalid sign up data', 400);
        }
    }

    /**
     * Endpoint /login
     * Trata de iniciar sesión con un email y password pasados en el request body en JSON
     * Si se puede iniciar sesión devuelve 2000OK, los datos del usuario y su access y refresh tokens
     * Si no puede, devuelve 400 bad request
     */
    public static function login(){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad request si faltan campos
        if(!isset($data['email'])) returnHTTPError('Email not provided', 400);
        if(!isset($data['password'])) returnHTTPError('Passwot not provided', 400);

        $model = new UserModel();

        $user = $model->logInUser($data['email'], password: $data['password']);

        if(!isset($user)){
            returnHTTPError('User not found', 404);
        }
        else{
            http_response_code(200);
            header('Content-Type: application/json');
            
            $accessToken = JWTUtils::createAccessToken($user->getId());
            $refreshToken = JWTUtils::createRefreshToken($user->getId());

            $response = array('userData' => $user->jsonSerialize(), 'accessToken' => $accessToken, 'refreshToken' => $refreshToken);

            echo json_encode($response);
            exit;
        }
    }

    /**
     * Endpoint /user
     * Actualiza los datos del usuario con sesión iniciada en la aplicación. Puede recibir como parámetros:
     * name, lastname, email, oldPassword, newPassword, id_Document, birthday, city, address, postal_code,
     * phone, card_name, card_number, cvv. Se actualizarán lso datos pasados.
     * @param int $userID ID del usuario a actualizar obtenida del JWT access token
     */
    public static function patchUser(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        $name = $lastname = $email = $oldPassword = $newPassword = $id_document = $birthday = $city = $address = $postal_code = $phone = $card_name = $card_number = $cvv= null;

        //Bad Request si faltan campos
        if(isset($data['name'])) $name = $data['name'];
        if(isset($data['lastname'])) $lastname = $data['lastname'];
        if(isset($data['email'])) $email = $data['email'];

        //Se deben enviar tanto la contraseña antigua como la nueva en una misma llamada para poder actualizarla
        if(isset($data['oldPassword']) || isset($data['newPassword'])){
            if(!isset($data['newPassword']) || !isset($data['oldPassword'])){
                returnHTTPError('Old password and new password must both be provided to update password', 400);
            }
            else{
                $oldPassword = $data['oldPassword'];
                $newPassword = $data['newPassword'];
            }      
        }
        if(isset($data['newPassword'])) $newPassword = $data['newPassword'];
        if(isset($data['id_document'])) $id_document = $data['id_document'];
        if(isset($data['birthday'])) $birthday = $data['birthday'];
        if(isset($data['city'])) $city = $data['city'];
        if(isset($data['address'])) $address = $data['address'];
        if(isset($data['postal_code'])) $postal_code = $data['postal_code'];
        if(isset($data['phone'])) $phone = $data['phone'];
        if(isset($data['card_name'])) $card_name = $data['card_name'];
        if(isset($data['card_number'])) $card_number = $data['card_number'];
        if(isset($data['cvv'])) $cvv = $data['cvv'];

        $model = new UserModel();
        $updated = $model->updateUser(
            $userID, 
            $name, 
            $email, 
            $oldPassword, 
            $newPassword, 
            $lastname, 
            $id_document, 
            $birthday, 
            $city, 
            $address, 
            $postal_code, 
            $phone, 
            $card_name, 
            $card_number, 
            $cvv);

        if($updated){
            //Se devuelve 201 Created
            http_response_code(200);
            $response = array('message' => "User updated");
            echo json_encode($response);
            exit;
        }
        else{
            returnHTTPError('Invalid user update data', 400);
        }
    }

    public static function getUserData(int $userID){
        $model = new UserModel();
        $userData = $model->getUserData($userID);

        if(!isset($userData)) returnHTTPError('Invalid user', 400);

        http_response_code(200);
        $response = array('user' => $userData->jsonSerialize());
        echo json_encode($response);
        exit;
    }
}
?>