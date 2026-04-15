<?php 
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php');
class BookModel{
    /**
     * Devuelve un objeto de conexión a la base de datos MySQLi.
     * Devuelve null si no se puede conectar.
     * @param string $server servidor de la base de datos
     * @param string $database nombre de la base de datos
     * @param string $user usuario de conexión
     * @param string $password contraseña de conexión
     * @return mysqli|null
     */
    private function connect(string $server, string $database, string $user, string $password)
    {
        $mysqli = new mysqli($server, $user, $password, $database);

        if ($mysqli->connect_error)  return null;
        return $mysqli;
    }

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
        string $category = ""
        ): bool
    {
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);

        //Se cancela si hay un error de conexión
        if ($connection->error) {
            $connection->close();
            return false;
        }

        $connection->autocommit(false);
        $connection->begin_transaction();
       
        $query = $connection->prepare('INSERT INTO books (title, author, description, rent_price, sell_price, isbn, url, in_cart, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $inCart = false;
        $query->bind_param('sssddssss', $title, $author, $description, $rent_price, $sell_price, $isbn, $url, $inCart, $category);

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

    public function deleteBook(int $book_id){
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);

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

    public function getAllBooks(){

    }

    public function getAllBooksFromUser(int $userID){

    }

    public function getAllBooksByCategory(string $category){

    }
}
?>