<?php
require_once(dirname(dirname(__FILE__)) . '/Database/UserModel.php');
/**
 * Endpoint /signup
 * Inserta un usuario dado su name, email y password en la base de datos.
 * 200OK si puede hacerlo, 400 si no
 */
function signup(){
    //Se recupera el body de la request en formato JSON
    $inputJSON = file_get_contents('php://input');
    $signupData = json_decode($inputJSON, TRUE);

    //Bad Request si faltan campos
    if(!isset($signupData['name'])) returnHTTPError('Name not provided', 400);
    if(!isset($signupData['email'])) returnHTTPError('Email not provided', 400);
    if(!isset($signupData['password'])) returnHTTPError('Password not provided', 400);

    $model = new UserModel();

    $inserted = $model->signUpUser($signupData['name'], $signupData['email'], password: $signupData['password']);

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