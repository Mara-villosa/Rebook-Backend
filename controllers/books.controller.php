<?php 
require_once(ROOT . '/database/models/BookModel.php');
class BooksController{
    /**
     * Endpoint /books/new
     * Recibe por POST los datos de un nuevo libro a crear en la base de datos
     */
    public static function uploadBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['title'])) returnHTTPError('Title not provided', 400);
        if(!isset($signupData['description'])) returnHTTPError('Description not provided', 400);
        if(!isset($signupData['author'])) returnHTTPError('Author not provided', 400);
        if(!isset($signupData['isbn'])) returnHTTPError('ISBN not provided', 400);
        if(!isset($signupData['url'])) returnHTTPError('Cover URL image not provided', 400);
        if(!isset($signupData['category'])) returnHTTPError('Category not provided', 400);

        if(!isset($signupData['rent_price']) && !isset($signupData['sell_price'])) 
            returnHTTPError('Rent or Sell price must be provided', 400);

        $rent_price = -1;
        if(isset($signupData['rent_price'])){
            $rent_price = $signupData['rent_price'];
            if($rent_price <= 0) returnHTTPError('Rent price must be higher than 0', 400);
        }
        
        $sell_price = -1;
        if(isset($signupData['sell_price'])){
            $sell_price = $signupData['sell_price'];
            if($sell_price <= 0) returnHTTPError('Sell price must be higher than 0', 400);
        }

        $model = new BookModel();
        $created = $model->createBook(
            $signupData['title'],
            $signupData['description'], 
            $signupData['author'],
            $rent_price, 
            $sell_price, 
            $signupData['isbn'], 
            $signupData['url'],
            $signupData['category'],
            $userID);

        if($created){
            //Se devuelve 201 Created
            http_response_code(201);
            $response = array('message' => "Book Created");
            echo json_encode($response);
            exit;
        }
        else{
            returnHTTPError('Invalid book data', 400);
        }
    }
    /**
     * Endpoint /books/delete
     * Borrar un libro de la base de datos con una id especificada
     */
    public static function deleteBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new BookModel();
        $deleted = $model->deleteBook($signupData['book_id'], $userID);

        if($deleted){
            http_response_code(200);
            $response = array('message' => "Book deleted");
            echo json_encode($response);
            exit;
        }
        else{
            returnHTTPError('Invalid book data', 400);
        }
    }

    /**
     * Endpoint /books/getAll
     * Devuelve todos los libros de la base de datos
     */
    /**
     * Endpoint /books/user
     * Recupera todos los libros que ha subido, comprado y alquilado un usuario
     */
    public static function getAllBooks(){
        $model = new BookModel();
        $books = $model->getAllBooks();

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

    /**
     * Endpoint /books/getFromUser
     * Devuelve un array con todos los libros comprados, subidos y alquilados por un usuario
     * @param string $userID usuario que hace la petición
     */
    public static function getAllBooksFromUser(string $userID){
        $model = new BookModel();
        $books = $model->getAllBooksFromUser($userID);

        if(!isset($books)) returnHTTPError('Books not found', 404);

        http_response_code(200);
        header('Content-Type: application/json');

        $response = array("uploads" => [], "rented" => [], "bought" => []);

        $uploads = array();
        $rented = array();
        $bought = array();
        for($i = 0; $i < count($books['uploads']); $i++){
            array_push($uploads, $books['uploads'][$i]->jsonSerialize());
        } 
        for($i = 0; $i < count($books['rented']); $i++){
            array_push($rented, $books['rented'][$i]->jsonSerialize());
        } 
        for($i = 0; $i < count($books['bought']); $i++){
            array_push($bought, $books['bought'][$i]->jsonSerialize());
        } 

        $response['uploads'] = $uploads;
        $response['rented'] = $rented;
        $response['bought'] = $bought;

        echo json_encode($response);
        exit;
    }
    /**
     * Endpoint /books/category
     * Devuelve un array con todos los libros de una categoría
     */
    public static function getAllBooksFromCategory(){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['category'])) returnHTTPError('Category not provided', 400);

        $model = new BookModel();
        $books = $model->getAllBooksByCategory($signupData['category']);

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

    /**
     * Endpoint /books/getBook
     * Devuelve los datos de un libro en concreto
     */
    public static function getBookDetails(){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new BookModel();
        $book = $model->getBookDetails($signupData['book_id']);

        if(!isset($book)) returnHTTPError('Books not found', 404);

        http_response_code(200);
        header('Content-Type: application/json');

        $response = $book->jsonSerialize();
        echo json_encode($response);
        exit;
    }
}
?>