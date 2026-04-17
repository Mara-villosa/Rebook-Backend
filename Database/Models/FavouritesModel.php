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

    /**
     * Elimina un libro de la tabla de favoritos para un usuario
     * @param int $bookID
     * @param int $userID
     * @return bool true si se elimina
     */
    public function removeBookFromFav(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Quitar el libro de la tabla rented
        $query = $connection->prepare('DELETE FROM favourites WHERE favourites.id_book = ? AND favourites.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);

        //Si no se borra ningún libro es porque no estaba añadido como fav, se devuelve false
        try{
            $query->execute();
            if($query->affected_rows === 0){
                $connection->rollback();
                $connection->autocommit(true);
                return false;
            }
        }
        //Se cancela si hay un error
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return true;
    }


    /**
     * Devuelve todos los libros añadidos a la tabla favoritos por un usuario
     * @param int $userID
     * @return array|null array de libros si los encuentra, null si no tiene favoritos
     */
    public function getFavBooks(int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books INNER JOIN favourites ON books.id = favourites.id_book WHERE favourites.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $books = array();
        while($result = $query_result->fetch_assoc()){
            $book = new BookDTO(
                $result['id'],
                $result['title'],
                $result['author'],
                $result['description'],
                $result['rent_price'],
                $result['sell_price'],
                $result['isbn'],
                $result['url'],
                $result['category'],
                $result['in_cart'],
                $result['rented'], 
                $result['id_user'],
                $result['sold'],
                $result['rent_expiration_date']);

            array_push($books, $book);
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $books;
    }
}
?>