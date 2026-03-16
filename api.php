
<?php
require 'vendor/autoload.php';
require_once('./API/checkHeaders.php');
require_once('./API/login.php');
require_once('./API/signup.php');
require_once('./API/refresh.php');

//CORS Policy
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = explode( 'api.php', $uri )[1];

//Necesitan una cabecera x-api-key válida
$public_uri = array("/login", "/signup", "/refresh");

//Necesitan una cabecera Authorization: Bearer JWT válida
$private_uri = array(""); 

//Llamadas públicas a la API (no necesitan autenticación)
if(in_array($request, $public_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(checkValidPublicAPICall()){
        switch($request){
            case '/login': 
                login();
                break;
            case '/signup':
                signup();
                break;
            case '/refresh': 
                refresh();
                break;
            }
    }
    else returnHTTPError("Invalid api key", 401);
}

//Llamadas privadas a la API (necesitan autenticación)
else if(in_array($request, $private_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(checkValidPrivateAPICall()){
        switch($request){
            }
    }
    else returnHTTPError("Invalid access token", 401);    
}
//Cualquier otra ruta devuelve 404 Not Found
else returnHTTPError('Page not Found', 404);


/**
 * Devuelve un mensaje de error y un código de error y finaliza el programa
 */
function returnHTTPError(string $errorMessage, int $errorCode){
    $response = array('message' => $errorMessage);
    http_response_code($errorCode);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>