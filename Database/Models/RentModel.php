<?php 
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php');

class RentModel{
    /**
     * Alquila un libro para el usuario que hace la petición durante dos semanas
     * @param int $bookID
     * @param int $userID
     * @return string|null
     */
    public function rentBook(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();;
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM rented WHERE rented.id_book = ?');
        $query->bind_param('i', $bookID);
        $query->execute();
        $query_result = $query->get_result();

        //El libro ya está alquilado
        if($query_result->num_rows > 0){
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $query = $connection->prepare('INSERT INTO rented (id_user, id_book, rented_on, expiration_date) VALUES (?,?,?,?)');

        $currentDate = date('Y-m-d');
        $enddate = strtotime("+2 weeks", strtotime($currentDate));
        $expirationDate = date('Y-m-d', $enddate);

        $query->bind_param('iiss', $userID, $bookID, $currentDate ,$expirationDate);

        try{
            $query->execute();
        }
        //Se cancela si hay un error de inserción
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query->affected_rows === 0) {
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $query = $connection->prepare('UPDATE books SET rented = ?, rent_expiration_date = ? WHERE books.id = ?');

        $rented = true;
        $query->bind_param('ssi', $rented, $expirationDate, $bookID);
        $query->execute();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query->affected_rows === 0) {
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $expirationDate;
    }

    /**
     * Devuelve todos los libros alquilados por el usuario que hace la petición. Null si no hay ninguno
     * @param int $userID
     * @return array|null
     */
    public function getRentedBooks(int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Todos los libros alquilados por un usuario
        $rented = array();
        $query = $connection->prepare('SELECT * FROM books INNER JOIN rented ON books.id=rented.id_book WHERE rented.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuetra el libro, devuelve null
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

    /**
     * Devuelve la fecha de expiración de un libro, o null si no lo encuentra
     * @param int $bookID
     * @param int $userID
     */
    public function checkRent(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Todos los libros alquilados por un usuario
        $query = $connection->prepare('SELECT * FROM rented WHERE rented.id_book = ? AND rented.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el libro, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $result = $query_result->fetch_assoc();

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $result['expiration_date'];
    }

    /**
     * Extiende la fecha de devolución de un libro alquilado por 2 semanas
     * @param int $bookID
     * @param int $userID
     * @return string|null
     */
    public function extendRent(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT expiration_date FROM rented WHERE rented.id_book = ? AND rented.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra el libro, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $result = $query_result->fetch_assoc();
        $old_expiration_date = $result['expiration_date'];
        $enddate = strtotime("+2 weeks", strtotime($old_expiration_date));
        $new_expiration_date = date('Y-m-d', $enddate);

        $query = $connection->prepare('UPDATE rented SET expiration_date = ? WHERE rented.id_book = ? AND rented.id_user = ?');
        $query->bind_param('sii', $new_expiration_date, $bookID, $userID);
        $query->execute();

        //Si hay un error o no ha actualizado el libro, devuelve null
        if ($connection->error || $query->affected_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $query = $connection->prepare('UPDATE books SET rent_expiration_date = ? WHERE id = ?');
        $query->bind_param('si', $new_expiration_date, $bookID);
        $query->execute();

        //Si hay un error o no encuentra el usuario, devuelve null
        if ($connection->error || $query->affected_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return null;
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $new_expiration_date;
    }

    /**
     * Elimina un libro de la tabla de alquilados y establece las propiedades rented, rent
     * @param int $bookID
     * @param int $userID
     * @return void
     */
    public function returnRentedBook(int $bookID, int $userID): bool{
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Quitar el libro de la tabla rented
        $query = $connection->prepare('DELETE FROM rented WHERE rented.id_book = ? AND rented.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);

        try{
            $query->execute();
            if($query->affected_rows === 0){
                $connection->rollback();
                $connection->autocommit(true);
                return false;
            }
        }
        //Se cancela si hay un error de inserción
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        //Establecer en la tabla de libros las propiedades del libro rented a false y expiration date a null
        $query = $connection->prepare('UPDATE books SET rented = ?, rent_expiration_date = ? WHERE id = ?');

        $rented = false;
        $expiration_date = null;
        $query->bind_param('ssi', $rented, $expiration_date, $bookID);

        try{
            $query->execute();
            if($query->affected_rows <= 0){
                $connection->rollback();
                $connection->autocommit(true);
                return false;
            }
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
}
?>