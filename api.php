<?php
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Rebook-Backend');

require 'vendor/autoload.php';
require_once(ROOT . '/utils/checkHeaders.php');
require_once(ROOT . '/utils/CORS.php');
require_once(ROOT . '/controllers/users.controller.php');
require_once(ROOT . '/controllers/token.controller.php');
require_once(ROOT . '/controllers/books.controller.php');

handleCORS();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = explode( 'api.php', $uri )[1];

//Necesitan una cabecera x-api-key válida
$public_uri = array("/login", "/signup", "/refresh");

//Necesitan una cabecera Authorization: Bearer JWT válida
$private_uri = array("/user", "/books/new", "/books/delete", "/books/get", "/books/category", "/books/user"); 

//Llamadas públicas a la API (no necesitan autenticación)
if(in_array($request, $public_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(checkValidPublicAPICall()){
        switch($request){
            case '/login': 
                UsersController::login();
                break;
            case '/signup':
                UsersController::signup();
                break;
            case '/refresh': 
                TokenController::refresh();
                break;
            }
    }
    else returnHTTPError("Invalid api key", 401);
}

//Llamadas privadas a la API (necesitan autenticación)
else if(in_array($request, $private_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(checkValidPrivateAPICall()){
        $userID = getUserID();
        switch($request){
            case '/user': 
                UsersController::patchUser($userID);
                break;
            case '/books/new':
                BooksController::uploadBook();
                break;
            case '/books/delete':
                BooksController::deleteBook();
                break;
            case '/books/get':
                BooksController::getAllBooks();
                break;
            case '/books/category':
                BooksController::getAllBooksFromCategory();
                break;
            case '/books/user':
                BooksController::getAllBooksFromUser($userID);
                break;
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