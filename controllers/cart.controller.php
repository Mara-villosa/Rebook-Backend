<?php 
require_once(ROOT . '/database/models/CartModel.php');
class CartController{
    public static function addToCart(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);
        if(!isset($data['is_renting'])) returnHTTPError('Is renting parameter not provided', 400);

        $model = new CartModel();
        $result = $model->addBookToCart($data['book_id'], $userID, $data['is_renting']);

        if($result === 'Book already in cart' || $result === 'Book already rented or sold' || $result === 'Error adding book to cart')
            returnHTTPError($result, 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('message' => $result);

        echo json_encode($response);
        exit;
    }

    /**
     * Endpoint /cart/remove
     * Elimina un libro del carrito de un usuario
     * @param int $userID
     * @return never
     */
    public static function removeFromCart(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new CartModel();
        $removed = $model->removeBookFromCart($data['book_id'], $userID);

        if(!$removed) returnHTTPError('Cart removal failed', 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('message' => 'Book removed from cart');

        echo json_encode($response);
        exit;
    }

    /**
     * Endpoint /cart/get
     * Devuelve todos los libros que tiene un usuario añadidos al carrito
     * @param int $userID
     * @return never
     */
    public static function getCart(int $userID){
        $model = new CartModel();
        $books = $model->getBooksFromCart($userID);

        if(!isset($books)) returnHTTPError('Books not found', 404);

        http_response_code(200);
        header('Content-Type: application/json');

        $response = array("books" => []);
        for($i = 0; $i < count($books); $i++){
            array_push($response['books'], $books[$i]->jsonSerialize());
        } 

        echo json_encode($response);
        exit;
    }

    public static function buyCart(int $userID){

    }
}
?>