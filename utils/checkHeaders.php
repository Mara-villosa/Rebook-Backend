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
        $headers = getallheaders();
        if(!isset($headers['x-api-key'])) return false;
        
        $xApiKey = $headers['x-api-key'] ?? $headers['X-API-KEY'];

        if(!isset($xApiKey)) return false;

        return $xApiKey === HeaderUtils::api_key;
    }

    /**
     * Devuelve true si la llamada privada a la API contiene una cabecera
     * Authorization con un Bearer JWT válido
     * @return bool true si la cabecera es válida
     */
    public static function checkValidPrivateAPICall(): bool{
        $headers = getallheaders();
        if(!isset($headers['Authorization'])) return false;

        $authHeader = $headers['Authorization'] ?? $headers['authorization'];

        if(!isset($authHeader)) return false;

        if(!str_starts_with($authHeader, 'Bearer ')) return false;

        $token = substr($authHeader, 7);
        return JWTUtils::checkValidToken($token);
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