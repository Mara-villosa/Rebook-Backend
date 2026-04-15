<?php 
require_once(ROOT . '/database/models/BookModel.php');
class BooksController{
    /**
     * Endpoint /books/new
     * Recibe por POST los datos de un nuevo libro a crear en la base de datos
     */
    public static function uploadBook(){
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

        isset($signupData['rent_price']) ? $rent_price = $signupData['rent_price'] : $rent_price = -1;
        if($rent_price <= 0) returnHTTPError('Rent price must be higher than 0', 400);

        isset($signupData['sell_price']) ? $sell_price = $signupData['sell_price'] : $sell_price = -1;
        if($sell_price <= 0) returnHTTPError('Sell price must be higher than 0', 400);

        $model = new BookModel();
        $created = $model->createBook(
            $signupData['title'],
            $signupData['description'], 
            $signupData['author'],
            $rent_price, 
            $sell_price, 
            $signupData['isbn'], 
            $signupData['url'],
            $signupData['category']);

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
    public static function deleteBook(){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $signupData = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($signupData['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new BookModel();
        $deleted = $model->deleteBook($signupData['book_id']);

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

    //Debería devolver para cada libro si está alquilado o no
    public static function getAllBooks(){
        $model = new BookModel();
        

    }
    public static function getAllBooksFromUser(string $userID){

    }
    public static function getAllBooksFromCategory(){

    }
}
?>