<?php
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php'); 
class FavouritesModel{
    /**
     * Añade un libro a la tabla de favoritos asociado a un usuario
     * @param int $bookID
     * @param int $userID
     * @return bool true si se añade
     */
    public function addBookToFav(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Comprobar si el libro está en la base de datos
        $query = $connection->prepare('SELECT * FROM books WHERE books.id = ?');
        $query->bind_param('i', $bookID);
        $query->execute();
        $query_result = $query->get_result();
        //Si hay error o no se encuentra el libro, se devuelve false
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        //Comprobar si el libro ya está añadido a favoritos por este usuario
        $query = $connection->prepare('SELECT * FROM favourites WHERE favourites.id_book = ? AND favourites.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);
        $query->execute();
        $query_result = $query->get_result();
        //Si hay error o si el libro ya está en favoritos por este usuario, se devuelve false
        if ($connection->error || $query_result->num_rows > 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        //Añadir libro a tabla de favoritos
        $query = $connection->prepare('INSERT INTO favourites (id_book, id_user, added_on) VALUES (?, ?, ?)');
        $currentDate = date('Y-m-d');
        $query->bind_param('iis', $bookID, $userID, $currentDate);
        $query->execute();
        //Si hay error o el lbro no se ha añadio, se devuelve false
        if ($connection->error || $query->affected_rows === 0) {
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return true;
    }

    public function removeBookFromFav(int $bookID, int $userID){

    }

    public function getFavBooks(int $userID){

    }
}
?>