<?php
require_once(dirname(dirname(__FILE__)) . '/Database/UserModel.php');
/**
 * Endpoint /login
 * Trata de iniciar sesión con un email y contraseña pasados por POST (form-data).
 * Si se puede iniciar sesión devuelve 2000OK, los datos del usuario y su access y refresh tokens
 * Si no puede, devuelve 400 bad request
 * @return never
 */
function login(){
    if(!isset($_POST['email'])) returnBadRequest();
    if(!isset($_POST['password'])) returnBadRequest();

    $model = new UserModel();

    $user = $model->logInUser($_POST['email'], $_POST['password']);

    if(!isset($user)){
        returnBadRequest();
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