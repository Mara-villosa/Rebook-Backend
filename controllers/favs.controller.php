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

    /**
     * Endpoint /fav/remove
     * Elimina un libro de favoritos para el usuario que hace la petición
     * @param int $userID
     */
    public static function removeFavBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);

        $model = new FavouritesModel();
        $removed = $model->removeBookFromFav($data['book_id'], $userID);

        if(!$removed) returnHTTPError('Fav removal failed', 400);
        
        http_response_code(200);
        header('Content-Type: application/json');

        $response = array('message' => 'book removed from favourites');

        echo json_encode($response);
        exit;
    }

    /**
     * Endpoint /fav/get
     * Recupera un array con todos los favoritos de un usuario
     * @param int $userID
     */
    public static function getFavBooks(int $userID){
        $model = new FavouritesModel();
        $books = $model->getFavBooks($userID);

        if(!isset($books)) returnHTTPError('Fav Books not found for this user', 404);

        http_response_code(200);
        header('Content-Type: application/json');

        $response = array("favourites" => []);
        for($i = 0; $i < count($books); $i++){
            array_push($response['favourites'], $books[$i]->jsonSerialize());
        } 

        echo json_encode($response);
        exit;
    }
 }
?>