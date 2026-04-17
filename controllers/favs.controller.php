<?php 
require_once(ROOT . '/database/models/FavouritesModel.php');
 class FavsController{
    public static function addFavBook(int $userID){
        //Se recupera el body de la request en formato JSON
        $inputJSON = file_get_contents('php://input');
        $data = json_decode($inputJSON, TRUE);

        //Bad Request si faltan campos
        if(!isset($data['book_id'])) returnHTTPError('Book ID not provided', 400);
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