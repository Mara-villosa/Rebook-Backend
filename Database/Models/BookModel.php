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

        if ($mysqli->connect_error) {
            return null;
        }

        return $mysqli;
    }

    public function createBook(){

    }

    public function deleteBook(){

    }

    public function getAllBooks(){

    }

    public function getAllBooksFromUser(int $userID){

    }

    public function getAllBooksByCategory(string $category){

    }

}
?>