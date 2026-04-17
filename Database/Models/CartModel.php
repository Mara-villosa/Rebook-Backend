<?php 
require_once(ROOT . '/database/databaseConfig.php');
require_once(ROOT . '/database/DTOs/BookDTO.php');
require_once(ROOT . '/database/models/RentModel.php');
class CartModel{
    /**
     * Añade un libro a la tabla de carts y cambia su propiedad in_cart a true
     * @param int $bookID
     * @param int $userID
     * @param bool $renting
     * @return bool true si se ha añadido el libro
     */
    public function addBookToCart(int $bookID, int $userID, bool $renting): string{
        $connection = DatabaseConfig::connect();;
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Comprobar que el libro existe y no está comprado, alquilado o en otro carrito
        $query = $connection->prepare('SELECT * FROM carts WHERE carts.id_book = ?');
        $query->bind_param('i', $bookID);
        $query->execute();
        $query_result = $query->get_result();

        //El libro ya está en el carrito
        if($query_result->num_rows > 0){
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return 'Book already in cart';
        }

        $query = $connection->prepare('SELECT * FROM books WHERE books.id = ? AND (books.rented = TRUE OR books.sold = TRUE)');
        $query->bind_param('i', $bookID);
        $query->execute();
        $query_result = $query->get_result();

        //El libro ya está comprado o alquilado
        if($query_result->num_rows > 0){
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return 'Book already rented or sold';
        }

        //Añadir a la tabla de carrito
        $query = $connection->prepare('INSERT INTO carts (id_user, id_book, is_rent) VALUES (?,?,?)');
        $query->bind_param('iis', $userID, $bookID, $renting);

        try{
            $query->execute();
        }
        //Se cancela si hay un error de inserción
        catch(Exception $e){
            $connection->rollback();
            $connection->autocommit(true);
            return 'Error adding book to cart';
        }

        //Si hay un error o no inserta el libro, devuelve null
        if ($connection->error || $query->affected_rows === 0) {
            $connection->rollback();
            $connection->autocommit(true);
            return 'Error adding book to cart';
        }

        //Cambiar en las propiedades del libro que ha sido añadido a un carrito
        $query = $connection->prepare('UPDATE books SET in_cart = TRUE WHERE books.id = ?');

        $query->bind_param('i', $bookID);
        $query->execute();

        //Si hay un error o no ha actualizado el libro, devuelve false
        if ($connection->error || $query->affected_rows === 0) {
            $connection->rollback();
            $connection->autocommit(true);
            return 'Error adding book to cart';
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

            return 'Book added to cart';
    }

    /**
     * Elimina un libro de la tabla de carts para un usuario
     * @param int $bookID
     * @param int $userID
     * @return bool
     */
    public function removeBookFromCart(int $bookID, int $userID){
        $connection = DatabaseConfig::connect();;
        $connection->autocommit(false);
        $connection->begin_transaction();

        //Borrar de la tabla de carrit
        $query = $connection->prepare('DELETE FROM carts WHERE carts.id_book = ? AND carts.id_user = ?');
        $query->bind_param('ii', $bookID, $userID);
        $query->execute();

        //El libro no estaba en el carrito
        if($query->affected_rows === 0){
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        //Actualizar datos del libro
        $query = $connection->prepare('UPDATE books SET in_cart = FALSE WHERE books.id = ?');
        $query->bind_param('i', $bookID);
        $query->execute();

        //Si hay un error o no ha actualizado el libro devuelve false
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
     * Devuelve todos los libros de la tabla carts de un usuario
     * @param int $userID
     * @return array | null array de libros o null si no encuentra ninguno
     */
    public function getBooksFromCart(int $userID): array |null{
        //Select de todos los libros de la tabla
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books INNER JOIN carts ON books.id = carts.id_book WHERE carts.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra libros, devuelve null
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
            $book->setInCartForRent($result['is_rent']);

            array_push($books, $book);
        }

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return $books;
    }

    /**
     * Comprueba, por cada libro añadido al carrito si se ha añadido para alquilar o no. 
     * Si se ha añadido para alquilar, se usa rentBook de rentModel para alquilarlo. 
     * Si se ha añadido para comprar, se añade a la tabla bought.
     * Después, se vacía el carrito del usuario
     * @param int $userID
     * @return bool|string|null
     */
    public function buyBooksInCart(int $userID){
        //Select de todos los libros de la tabla
        $connection = DatabaseConfig::connect();
        $connection->autocommit(false);
        $connection->begin_transaction();

        $query = $connection->prepare('SELECT * FROM books INNER JOIN carts ON books.id = carts.id_book WHERE carts.id_user = ?');
        $query->bind_param('i', $userID);
        $query->execute();
        $query_result = $query->get_result();

        //Si hay un error o no encuentra libros, devuelve null
        if ($connection->error || $query_result->num_rows === 0) {
            $query_result->free();
            $connection->rollback();
            $connection->autocommit(true);
            return false;
        }

        $books = $query_result->fetch_all(MYSQLI_ASSOC);

        //Recorrer libros del carrito y operar según si se han añadido para alquilar o para comprar
        for($i = 0; $i < count($books); $i++){
            //El libro se ha añadido par aalquilar
            if($books[$i]['is_rent']){
                //Quitar propiedad de carrito in_cart del carrito (no se pueden alquilar libros en el carrito)
                $query = $connection->prepare('UPDATE books SET in_cart = FALSE WHERE books.id = ?');
                $query->bind_param('i', $books[$i]['id']);
                $query->execute();

                if ($connection->error || $query->affected_rows === 0) {
                    $connection->rollback();
                    $connection->autocommit(true);
                    return false;
                }

                //Alquilar el libro
                $rentModel = new RentModel();
                $expirationDate = $rentModel->rentBookFromCart($books[$i]['id'], $userID, $connection);

                if(!isset($expirationDate)){
                    $connection->rollback();
                    $connection->autocommit(true);
                    return false;
                }

            }
            //El libro se ha añadido para Comprar
            else{
                //Actualizar propiedad sold del libro a true
                $query = $connection->prepare('UPDATE books SET sold = TRUE, in_cart = FALSE WHERE books.id = ?');
                $query->bind_param('i', $books[$i]['id']);
                $query->execute();

                if ($connection->error || $query->affected_rows === 0) {
                    $connection->rollback();
                    $connection->autocommit(true);
                    return false;
                }

                //Añadirlo a la tabla de comprados
                $query = $connection->prepare('INSERT INTO bought (id_user, id_book, bought_on) VALUES (?,?,?)');
                $currentDate = date('Y-m-d');
                $query->bind_param('iis', $userID, $books[$i]['id'], $currentDate);

                try{
                    $query->execute();
                }
                //Se cancela si hay un error de inserción
                catch(Exception $e){
                    $connection->rollback();
                    $connection->autocommit(true);
                    return false;
                }

                //Si hay un error o no se inserta el libro, devuelve null
                if ($connection->error || $query->affected_rows === 0) {
                    $connection->rollback();
                    $connection->autocommit(true);
                    return false;
                }
            }
        }

        //Después borrar todas las filas de carrito de este usuario
        $query = $connection->prepare('DELETE FROM carts WHERE carts.id_user = ?');
        $query->bind_param('i', $userID);

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

        $query_result->free();
        $connection->commit();
        $connection->autocommit(true);
        $connection->close();

        return true;
    }
}
?>