<?php 
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php');
class BookModel{
    /**
     * Añade un libro nuevo a la base de datos
     * @param string $title
     * @param string $description
     * @param string $author
     * @param float $rent_price
     * @param float $sell_price
     * @param string $isbn
     * @param string $url
     * @param string $category
     * @return bool true si se ha podido añadir, false si no
     */
    public function createBook(
        string $title, 
        string $description, 
        string $author = "", 
        float $rent_price = -1,
        float $sell_price = -1, 
        string $isbn = "",
        string $url = "",
        string $category = "",
        int $userID
        ): bool
    {
        $connection = DatabaseConfig::connect();

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
       
        $query = $connection->prepare('INSERT INTO books (title, author, description, rent_price, sell_price, isbn, url, category, in_cart, rented, id_user, rent_expired, sold, rent_expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $inCart = $rented = $rent_expired = $sold = false;
        $category = strtolower($category);
        $rent_expiration_date = '';
        $query->bind_param('sssddsssssisss', $title, $author, $description, $rent_price, $sell_price, $isbn, $url, $category, $inCart, $rented, $userID, $rent_expired, $sold, $rent_expiration_date);

        try{
            $query->execute();
        }
        //Se cancela si hay un error de inserción
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
     * Borrar un libro con id especificada de la base de datos
     * @param int $book_id
     * @return bool true si se ha podido borrar
     */
    public function deleteBook(int $book_id){
        $connection = DatabaseConfig::connect();

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
       
        $query = $connection->prepare('DELETE FROM books WHERE ID = ?');
        $query->bind_param('i', $book_id);

        $affectedRows = -1;

        try{
            $query->execute();
            $affectedRows = $query->affected_rows;
        }
        //Se cancela si hay un error de inserción
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }        

        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $affectedRows > 0;
    }

    /**
     * Devuelve un array de BookDTO con todos los libros recuperados de la base de datos
     * @returns array | null
     */
    public function getAllBooks(): array|null{
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books');
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

    /**
     * Devuelve un array con todos los libros subidos, comprados y alquilados por un usuario
     * @param int $userID
     * @return array|null
     */
    public function getAllBooksFromUser(int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $result = array('uploads' => [], 'rented' => [], 'bought' => []);
        $uploads = array();

        //Todos los libros subidos por un usuario
        $query = $connection->prepare('SELECT * FROM books WHERE id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

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

            array_push($uploads, $book);
        }

        //Todos los libros alquilados por un usuario
        $rented = array();
        $query = $connection->prepare('SELECT * FROM books INNER JOIN rented ON books.id=rented.id_book WHERE rented.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

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

            array_push($rented, $book);
        }

        //Todos los libros comprados
        $bought = array();
        $query = $connection->prepare('SELECT * FROM books INNER JOIN bought ON books.id=bought.id_book WHERE bought.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        if($connection->error){
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

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

            array_push($bought, $book);
        }
        $result['uploads'] = $uploads;
        $result['rented'] = $rented;
        $result['bought'] = $bought;

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $result;
    }

    /**
     * Devuelve un array con todos los libros de una categoría
     * @param string $category categoría a buscar
     */
    public function getAllBooksByCategory(string $category){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books WHERE category = ?');
        $query->bind_param('s', $category);
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

    /**
     * Devuelve los datos de un libro en concreto
     * @param int $book_id id del libro a buscar
     */
    public function getBookDetails(int $book_id){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books WHERE id = ?');
        $query->bind_param('i', $book_id);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $result = $query_result->fetch_assoc();

        if(!isset($result)){
            $query_result->free();
            $connection->autocommit(true);
            return null;
        }

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

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $book;
    }
}
?>