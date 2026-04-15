<?php
require_once(ROOT . '/utils/JWT.php');
class HeaderUtils{
    //API Key del servidor para llamadas públicas
    const api_key = "d6o06RFU8bwKUGftmVQ2Caj9OHarGZdN";

    /**
     * Devuelve true si la llamada pública a la API contiene una cabecera
     * x-api-key con la API key correcta
     * @return bool true si la cabecera es válida
     */
    public static function checkValidPublicAPICall(): bool{
        foreach (getallheaders() as $name => $value) {
            if($name === 'x-api-key' && $value === HeaderUtils::api_key){
                return true;
            }
        }
        return false;
    }

    /**
     * Devuelve true si la llamada privada a la API contiene una cabecera
     * Authorization con un Bearer JWT válido
     * @return bool true si la cabecera es válida
     */
    public static function checkValidPrivateAPICall(): bool{
        foreach (getallheaders() as $name => $value) {
            if($name === 'Authorization'){
                //La cabecera tiene formato 'Bearer tokenJWT'
                //Se separa el tokenJWT y se comprueba su validez
                $token = explode(' ', $value)[1];

                return JWTUtils::checkValidToken($token);
            }
        }
        return false;
    }
    /**
     * Devuelve el ID del usuario extraído del JWT de la cabecera Authorization
     * @return int
     */
    public static function getUserID(): int{
        foreach (getallheaders() as $name => $value) {
            if($name === 'Authorization'){
                //La cabecera tiene formato 'Bearer tokenJWT'
                //Se separa el tokenJWT y se comprueba su validez
                $token = explode(' ', $value)[1];
                $tokenDecoded = JWTUtils::getTokenDecoded($token);
                return $tokenDecoded->id;
            }
        }
        return -1;
    }
}
?>