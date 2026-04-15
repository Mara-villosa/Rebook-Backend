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
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['name'])) returnHTTPError('Name not provided', 400);
        if(!isset($signupData['lastname'])) returnHTTPError('Lastname not provided', 400);
        if(!isset($signupData['email'])) returnHTTPError('Email not provided', 400);
        if(!isset($signupData['password'])) returnHTTPError('Password not provided', 400);
        if(!isset($signupData['id_document'])) returnHTTPError('ID Document not provided', 400);
        if(!isset($signupData['birthday'])) returnHTTPError('Birthday not provided', 400);
        if(!isset($signupData['city'])) returnHTTPError('City not provided', 400);
        if(!isset($signupData['address'])) returnHTTPError('Address not provided', 400);
        if(!isset($signupData['postal_code'])) returnHTTPError('Postal Code not provided', 400);
        if(!isset($signupData['phone'])) returnHTTPError('Phone not provided', 400);

        //Campos opcionales de datos de la tarjeta
        $card_name = null;
        $card_number = null;
        $cvv = null;
        if(isset($signupData['card_name']) && isset($signupData['card_number']) && isset($signupData['cvv'])){
            $card_name = $signupData['card_name'];
            $card_number = $signupData['card_number'];
            $cvv = $signupData['cvv'];

            //Validación de datos de la tarjeta
            if(strlen($card_number) !== 16) returnHTTPError('Invalid card number', 400);
            if(strlen($cvv) !== 3) returnHTTPError('Invalid CVV', 400);
        } 

        $model = new UserModel();

        $inserted = $model->signUpUser(
            $signupData['name'], 
            $signupData['email'], 
            $signupData['password'],
            $signupData['lastname'],
            $signupData['id_document'], 
            $signupData['birthday'],
            $signupData['city'],
            $signupData['address'],
            $signupData['postal_code'],
            $signupData['phone'],
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
        $loginData = json_decode($inputJSON, TRUE);

        //Bad request si faltan campos
        if(!isset($loginData['email'])) returnHTTPError('Email not provided', 400);
        if(!isset($loginData['password'])) returnHTTPError('Passwot not provided', 400);

        $model = new UserModel();

        $user = $model->logInUser($loginData['email'], password: $loginData['password']);

        if(!isset($user)){
            returnHTTPError('User not found', 404);
        }
        else{
            http_response_code(200);
            header('Content-Type: application/json');
            
            $accessToken = createAccessToken($user->getId());
            $refreshToken = createRefreshToken($user->getId());

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
        $signupData = json_decode($inputJSON, TRUE);

        $name = $lastname = $email = $oldPassword = $newPassword = $id_document = $birthday = $city = $address = $postal_code = $phone = $card_name = $card_number = $cvv= null;

        //Bad Request si faltan campos
        if(isset($signupData['name'])) $name = $signupData['name'];
        if(isset($signupData['lastname'])) $lastname = $signupData['lastname'];
        if(isset($signupData['email'])) $email = $signupData['email'];

        //Se deben enviar tanto la contraseña antigua como la nueva en una misma llamada para poder actualizarla
        if(isset($signupData['oldPassword']) || isset($signupData['newPassword'])){
            if(!isset($signupData['newPassword']) || !isset($signupData['oldPassword'])){
                returnHTTPError('Old password and new password must both be provided to update password', 400);
            }
            else{
                $oldPassword = $signupData['oldPassword'];
                $newPassword = $signupData['newPassword'];
            }      
        }
        if(isset($signupData['newPassword'])) $newPassword = $signupData['newPassword'];
        if(isset($signupData['id_document'])) $id_document = $signupData['id_document'];
        if(isset($signupData['birthday'])) $birthday = $signupData['birthday'];
        if(isset($signupData['city'])) $city = $signupData['city'];
        if(isset($signupData['address'])) $address = $signupData['address'];
        if(isset($signupData['postal_code'])) $postal_code = $signupData['postal_code'];
        if(isset($signupData['phone'])) $phone = $signupData['phone'];
        if(isset($signupData['card_name'])) $card_name = $signupData['card_name'];
        if(isset($signupData['card_number'])) $card_number = $signupData['card_number'];
        if(isset($signupData['cvv'])) $cvv = $signupData['cvv'];

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
}
?>