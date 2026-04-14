<?php 
require_once(ROOT . '/database/models/UserModel.php');
/**
 * Actualiza los datos del usuario con sesión iniciada en la aplicación. Puede recibir como parámetros:
 * name, lastname, email, oldPassword, newPassword, id_Document, birthday, city, address, postal_code,
 * phone, card_name, card_number, cvv. Se actualizarán lso datos pasados.
 * @param int $userID ID del usuario a actualizar obtenida del JWT access token
 */
function patchUser(int $userID){
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
?>

