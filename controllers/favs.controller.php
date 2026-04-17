<?php 
require_once(ROOT . '/database/models/FavouritesModel.php');
 class FavsController{
    /**
     * Endpoint /fav/add
     * Añade un libro como favorito para el usuario que hace la petición
     * @param int $userID
     */
    public static function addFavBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new FavouritesModel();
        $added = $model->addBookToFav($data['book_id'], $userID);

        if(!$added) returnHTTPError('Fav addition failed', 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('message' => 'book added to favourites');

        echo json_encode($response);
        exit;
    }

    public static function removeFavBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);
    }

    public static function getFavBooks(int $userID){

    }
 }
?>