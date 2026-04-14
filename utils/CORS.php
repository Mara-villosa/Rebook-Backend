<?php 
    function handleCORS(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
        header("Access-Control-Max-Age: 86400");
        header("Vary: Origin");
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');
       
        // Handle preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit(0);
        }
    }
?>