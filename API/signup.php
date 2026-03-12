<?php
require_once(dirname(dirname(__FILE__)) . '/Database/UserModel.php');
/**
 * Inserta un usuario dado su name, email y password en la base de datos.
 * 200OK si puede hacerlo, 400 si no
 * @return never
 */
function signup(){
    if(!isset($_POST['name'])) returnBadRequest();
    if(!isset($_POST['email'])) returnBadRequest();
    if(!isset($_POST['password'])) returnBadRequest();

    $model = new UserModel();

    $inserted = $model->signUpUser($_POST['name'], $_POST['email'], $_POST['password']);

    if($inserted){
        http_response_code(200);
        exit;
    }
    else{
        returnBadRequest();
    }
}
?>