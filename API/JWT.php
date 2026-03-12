<?php
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Devuelve true si el token es válido y no está expirado
 * @param mixed $token
 * @return bool
 */
function checkValidToken($token): bool{
    $key = 'VihjFMf7MgywuPGrkJRlUOM0AhIyir0BTOcTszmKnCs=';
    try{
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        //Comprobar expiración
        return $decoded->exp > time();
    }
    //Excepción si el token no es un token JWT válido firmado con esta key y algoritmo
    catch (Exception $e){
        return false;
    }
}

/**
 * Crea un token de acceso para un usuario que contiene su id y tiene
 * expiración de 1 hora
 * @param mixed $userId id del usuario recuperada de la base de datos
 * @return string token de acceso codificado
 */
function createAccessToken($userId): string{
    $key = 'VihjFMf7MgywuPGrkJRlUOM0AhIyir0BTOcTszmKnCs=';
    $exp = time() + 3600;

    $payload = [
        'exp' => $exp,
        'id' => $userId
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
}

/**
 * Crea un token de refresco para un usuario que contiene su id y tiene
 * expiración de 1 día
 * @param mixed $userId id del usuario recuperada de la base de datos
 * @return string token de refresco codificado
 */
function createRefreshToken($userId): string{
    $key = 'VihjFMf7MgywuPGrkJRlUOM0AhIyir0BTOcTszmKnCs=';
    $exp = time() + 86400; //Expira en 1 día

    $payload = [
        'exp' => $exp,
        'id' => $userId
    ];

    $jwt = JWT::encode($payload, $key, 'HS256');
    return $jwt;
}
?>
