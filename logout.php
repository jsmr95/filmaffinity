<?php
session_start();
$_SESSION = []; //Liberamos el array
//Eliminamos la cookie
session_destroy();
header('Location: index.php');
