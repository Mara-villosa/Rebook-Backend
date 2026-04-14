<?php
require_once('./API/JWT.php');

//API Key del servidor para llamadas públicas
const api_key = "d6o06RFU8bwKUGftmVQ2Caj9OHarGZdN";

    /**
     * Devuelve true si la llamada pública a la API contiene una cabecera
     * x-api-key con la API key correcta
     * @return bool true si la cabecera es válida
     */
    function checkValidPublicAPICall(): bool{
        foreach (getallheaders() as $name => $value) {
            if($name === 'x-api-key' && $value === api_key){
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
    function checkValidPrivateAPICall(): bool{
        foreach (getallheaders() as $name => $value) {
            if($name === 'Authorization'){
                //La cabecera tiene formato 'Bearer tokenJWT'
                //Se separa el tokenJWT y se comprueba su validez
                $token = explode(' ', $value)[1];

                return checkValidToken($token);
            }
        }
        return false;
    }

    function getUserID(): int{
        foreach (getallheaders() as $name => $value) {
            if($name === 'Authorization'){
                //La cabecera tiene formato 'Bearer tokenJWT'
                //Se separa el tokenJWT y se comprueba su validez
                $token = explode(' ', $value)[1];
                $tokenDecoded = getTokenDecoded($token);
                return $tokenDecoded->id;
            }
        }
        return -1;
    }
?>