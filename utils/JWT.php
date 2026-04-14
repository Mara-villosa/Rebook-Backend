<?php
require_once(ROOT . '/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

//Llave privada del servidor usada para firmar el JWT
const key = 'VihjFMf7MgywuPGrkJRlUOM0AhIyir0BTOcTszmKnCs=';

/**
 * Crea un token de acceso para un usuario que contiene su id y tiene
 * expiración de 1 hora
 * @param mixed $userId id del usuario recuperada de la base de datos
 * @return string token de acceso codificado
 */
function createAccessToken(string $userId): string{
    $payload = [
        'exp' => time() + 3600, //Expira en 1 hora
        'id' => $userId
    ];

    $jwt = JWT::encode($payload, key, 'HS256');
    return $jwt;
}

/**
 * Crea un token de refresco para un usuario que contiene su id y tiene
 * expiración de 1 día
 * @param mixed $userId id del usuario recuperada de la base de datos
 * @return string token de refresco codificado
 */
function createRefreshToken(string $userId): string{
    $payload = [
        'exp' => time() + 86400, //Expira en 24 horas
        'id' => $userId
    ];

    $jwt = JWT::encode($payload, key, 'HS256');
    return $jwt;
}

/**
 * Devuelve true si el token es válido y no está expirado
 * @param mixed $token
 * @return bool
 */
function checkValidToken(string $token): bool{
    try{
        //Trata de decodificar el token con la firma del servidor guardada en key y el algoritmo HS256
        //Si falla lanza una excepción
        $decoded = JWT::decode($token, new Key(key, 'HS256'));

        //Devuelve true si el token no está expirado
        return $decoded->exp > time();
    }

    //El token no es un token JWT válido firmado con esta key y algoritmo
    catch (Exception $e){
        return false;
    }
}

/**
 * Devuelve un token JWT decodificado para acceder a su payload.
 * Si no puede decodificarlo devuelve null
 * @param string $token token a decodificar
 * @return stdClass|null
 */
function getTokenDecoded(string $token){
    try{
        //Trata de decodificar el token con la firma del servidor guardada en key y el algoritmo HS256
        //Si falla lanza una excepción
        $decoded = JWT::decode($token, new Key(key, 'HS256'));
        return $decoded;
    }

    //El token no es un token JWT válido firmado con esta key y algoritmo
    catch (Exception $e){
        return null;
    }
}
?>
