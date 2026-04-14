<?php
require_once(dirname(dirname(__FILE__)) . '/Database/Models/UserModel.php');
/**
 * Endpoint /signup
 * Inserta un usuario dado su name, email, password, lastname, id_document, birthday, city, address, postal_code y phone.
 * Opcionalmente, puede recibir card name, card number y cvv para registrar la tarjeta del usuario en la base de datos. 
 * 200OK si puede hacerlo, 400 si no
 */
function signup(){
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
?>