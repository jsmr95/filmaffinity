<?php
session_start();
$_SESSION = []; //Liberamos el array
//Eliminamos la cookie
$params = session_get_cookie_params();
setcookie(
    session_name(),
    '1',
    1,//Lo que hacemos es que con el ultimo 1, marcamos que expire el 1/1/1970, por lo que ya ha expirado, ya no existe.
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
);
session_destroy();
header('Location: index.php');
