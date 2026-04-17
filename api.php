<?php
define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Rebook-Backend');

require 'vendor/autoload.php';
require_once(ROOT . '/utils/checkHeaders.php');
require_once(ROOT . '/utils/CORS.php');
require_once(ROOT . '/controllers/users.controller.php');
require_once(ROOT . '/controllers/token.controller.php');
require_once(ROOT . '/controllers/books.controller.php');
require_once(ROOT . '/controllers/rent.controller.php');
require_once(ROOT . '/controllers/favs.controller.php');
require_once(ROOT . '/controllers/cart.controller.php');

CORSUtils::handleCORS();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = explode( 'api.php', $uri )[1];

//Necesitan una cabecera x-api-key válida
$public_uri = array(
"/login", "/signup", 
"/refresh", 
"/books/getAll", "/books/category", "/books/getBook");

//Necesitan una cabecera Authorization: Bearer JWT válida
$private_uri = array(
"/user", 
"/books/new", "/books/delete", "/books/getFromUser", 
"/rent", "/rent/check", "/rent/extend", "/rent/get", "/rent/return", 
"/fav/add", "/fav/remove", "/fav/get",
"/cart/add", "/cart/remove", "/cart/get", "/cart/buy"); 

//Llamadas públicas a la API (no necesitan autenticación)
if(in_array($request, $public_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(HeaderUtils::checkValidPublicAPICall()){
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
            case '/books/getAll':
                BooksController::getAllBooks();
                break;
            case '/books/category':
                BooksController::getAllBooksFromCategory();
                break;
            case '/books/getBook':
                BooksController::getBookDetails();
                break;
            }
    }
    else returnHTTPError("Invalid api key", 401);
}

//Llamadas privadas a la API (necesitan autenticación)
else if(in_array($request, $private_uri)){
    //Si la llamada no es válida se devuelve 400 bad request
    if(HeaderUtils::checkValidPrivateAPICall()){
        $userID = HeaderUtils::getUserID();
        switch($request){
            //User endpoints
            case '/user': 
                UsersController::patchUser($userID);
                break;
            //Book endpoints
            case '/books/new':
                BooksController::uploadBook($userID);
                break;
            case '/books/delete':
                BooksController::deleteBook();
                break;
            case '/books/getFromUser':
                BooksController::getAllBooksFromUser($userID);
                break;
            //Rent endpoints
            case '/rent':
                RentController::rent($userID);
                break;
            case '/rent/check':
                RentController::checkRent($userID);
                break;
            case '/rent/extend':
                RentController::extendRent($userID);
                break;
            case 'rent/get':
                RentController::getRented($userID);
                break;
            case '/rent/return':
                RentController::returnBook($userID);
                break;
            //Fav endpoints
            case '/fav/add':
                FavsController::addFavBook($userID);
                break;
            case '/fav/remove':
                FavsController::removeFavBook($userID);
                break;
            case '/fav/get':
                FavsController::getFavBooks($userID);
                break;
            //Cart endpoints
            case '/cart/add':
                CartController::addToCart($userID);
                break;
            case '/cart/remove':
                CartController::removeFromCart($userID);
                break;
            case '/cart/get':
                CartController::getCart($userID);
                break;
            case '/cart/buy':
                CartController::buyCart($userID);
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