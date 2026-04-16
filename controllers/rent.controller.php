<?php 
require_once(ROOT . '/database/models/RentModel.php');
class RentController{
    /**
     * Endpoint /rent/get 
     * Devuelve un array con todos los libros alquilados por el usuario
     * @param int $userID
     * @return never
     */
    public static function getRented(int $userID){
        $model = new RentModel();

        $rentedBooks = $model->getRentedBooks($userID);

        if(!isset($rentedBooks)) returnHTTPError('Books not found', 404);

        http_response_code(200);
        header('Content-Type: application/json');

        $response = array("books" => []);
        for($i = 0; $i < count($rentedBooks); $i++){
            array_push($response['books'], $rentedBooks[$i]->jsonSerialize());
        } 

        echo json_encode($response);
        exit;
    }
    
    /**
     * Endpoint /rent/check
     * Devuelve la fecha de expiración de un libro alquilado
     * @param int $userID
     * @return never
     */
    public static function checkRent(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new RentModel();
        $expirationDate = $model->checkRent($data['book_id'], $userID);

        if(!isset($expirationDate)) returnHTTPError('Book not found', 400);
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('expiration_date' => $expirationDate);

        echo json_encode($response);
        exit;
    }

    /**
     * Endpoint /rent/extend
     * Extiende el alquiler de un libro dos semanas
     * @param int $userID
     */
    public static function extendRent(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);
        $model = new RentModel();

        $expirationDate = $model->extendRent($data['book_id'], $userID);

        if(!isset($expirationDate)) returnHTTPError('Rent extension failed', 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('expiration_date' => $expirationDate);

        echo json_encode($response);
        exit;
    }

    /**
     * Endpoint /rent
     * Alquila un libro
     * @param int $userID
     * @return never
     */
    public static function rent(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new RentModel();
        $expirationDate = $model->rentBook($data['book_id'], $userID);

        if(!isset($expirationDate)) returnHTTPError('Rent failed', 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('expiration_date' => $expirationDate);

        echo json_encode($response);
        exit;
    }

    public static function returnBook(int $userID){

    }
}
?>