<?php 
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php');

class RentModel{
    /**
     * Devuelve un objeto de conexión a la base de datos MySQLi.
     * Devuelve null si no se puede conectar.
     * @param string $server servidor de la base de datos
     * @param string $database nombre de la base de datos
     * @param string $user usuario de conexión
     * @param string $password contraseña de conexión
     * @return mysqli|null
     */
    private function connect(string $server, string $database, string $user, string $password){
        $mysqli = new mysqli($server, $user, $password, $database);
        if ($mysqli->connect_error)  return null;
        return $mysqli;
    }

    public function rentBook(int $bookID, int $userID){

    }

    public function checkRent(int $bookID, int $userID){

    }

    public function extendRent(int $bookID, int $userID){

    }

    /**
     * Devuelve todos los libros alquilados por el usuario que hace la petición. Null si no hay ninguno
     * @param int $userID
     * @return array|null
     */
    public function getRentedBooks(int $userID){
        $connection = $this->connect(SERVER, DATABASE, USER, PASSWORD);
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Todos los libros alquilados por un usuario
        $rented = array();
        $query = $connection->prepare('SELECT * FROM books INNER JOIN rented ON books.id=rented.id_book WHERE rented.id_user = ?');
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
                $result['rent_expired'], 
                $result['sold'],
                $result['rent_expiration_date']);

            array_push($rented, $book);
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $rented;
    }
}
?>