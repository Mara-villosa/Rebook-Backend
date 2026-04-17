<?php
class DatabaseConfig{
    const SERVER = 'localhost'; //servidor de la base de datos
    const DATABASE = 'Rebook'; //nombre de la base de datos
    const USER = 'root'; //usuaroi de conexión
    const PASSWORD = ''; //contraseña de conexión

    /**
     * Devuelve un objeto de conexión a la base de datos MySQLi.
     * Devuelve null si no se puede conectar.
     * @return mysqli|null
     */
    public static function connect(){
        $mysqli = new mysqli(DatabaseConfig::SERVER, DatabaseConfig::USER, DatabaseConfig::PASSWORD, DatabaseConfig::DATABASE);
        if ($mysqli->connect_error)  return null;
        return $mysqli;
    }
}
?>