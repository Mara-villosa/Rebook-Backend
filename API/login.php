<?php
require_once(dirname(dirname(__FILE__)) . '/Database/Models/UserModel.php');
/**
 * Endpoint /login
 * Trata de iniciar sesión con un email y password pasados en el request body en JSON
 * Si se puede iniciar sesión devuelve 2000OK, los datos del usuario y su access y refresh tokens
 * Si no puede, devuelve 400 bad request
 */
function login(){
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
?>