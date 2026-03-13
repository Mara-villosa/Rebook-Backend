<?php
require_once(dirname(dirname(__FILE__)) . '/Database/UserModel.php');

/**
 * Recibe un refreshToken JWT y, si es válido y no está expirado, devuelve
 * un access token con expiración en 1 hora. 
 * @return void
 */
function refresh(){
    //Se recupera el body de la request en formato JSON
    $inputJSON = file_get_contents('php://input');
    $refreshData = json_decode($inputJSON, TRUE); //convert JSON into array

    if(!isset($refreshData['refreshToken'])) returnHTTPError('Refresh token not provided', 400);
    $refreshToken = $refreshData['refreshToken'];

    //Si el refreshToken no está expirado y es válido se devuelve un accessToken refrescado
    if(checkValidToken($refreshToken)){
        $refreshTokenDecoded = getTokenDecoded($refreshToken);
        $newAccessToken = createAccessToken($refreshTokenDecoded->id);

        $response = array('accessToken' => $newAccessToken, 'refreshToken' => $refreshToken);
        
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
        
    }
    else returnHTTPError('Invalid refresh token', 400);
}
?>