
<?php
require 'vendor/autoload.php';
require_once('./API/checkHeaders.php');
require_once('./API/login.php');
require_once('./API/signup.php');
require_once('./API/refresh.php');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = explode( 'api.php', $uri )[1];

$public_uri = array("/login", "/signup"); //Necesitan una x-api-key válida
$private_uri = array("/refresh"); //Necesitan una cabecera Authorization: Bearer JWT válida

//Llamadas públicas a la API (no necesitan autenticación)
if(in_array($request, $public_uri)){
    if(checkValidPublicAPICall()){
        switch($request){
            case '/login': 
                login();
                break;
            case '/signup':
                signup();
                break;
            }
    }
    else returnBadRequest();
}
//Llamadas privadas a la API (necesitan autenticación)
else if(in_array($request, $private_uri)){
    if(checkValidPrivateAPICall()){
        switch($request){
            case '/refresh': 
                refresh();
                break;
            }
    }
    else returnBadRequest();
    
    
}
//Cualquier otra ruta devuelve error Bad Request
else{
    returnBadRequest();
}

function returnBadRequest(){
    http_response_code(400); exit;
}
?>