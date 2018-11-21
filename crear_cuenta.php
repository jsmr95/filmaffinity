<?php session_start();
require 'comunes/auxiliar.php';
navegador();
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
  <div class="container">
  <?php
  compruebaSession('error','danger');

  $valores = PAR_CREAR_CUENTA;
  //Compruebo si ha fallado en el user para msotrarlos
  if (isset($_SESSION['userIncorrecto'])) {
    $valores['login'] = $_SESSION['userIncorrecto'];
    $_SESSION['userIncorrecto'] = '';
  }

  try{
     $error = [];
     $pdo = conectar();
     comprobarParametros(PAR_CREAR_CUENTA);
     $valores = array_map('trim', $_POST);
     $valores['login'] = comprobarLogin($error);
     $valores['password'] = comprobarPassword($error);
     $valores['passwordRepeat'] = comprobarPasswordNueva($valores['password'], $error);
     $usuario = comprobarUsuarioNuevo($valores,$pdo,$error);
     var_dump($error);
     comprobarErrores($error);
     if ($usuario) {
       $_SESSION['error'] = 'El usuario ya existe, debe escoger otro.';
       //Guardo las incorrectas para que se mantenga
       $_SESSION['userIncorrecto'] = $valores['login'];
       header('Location: crear_cuenta.php');
     }else {
       if (empty($error['passwordRepeat']) && $valores['passwordRepeat'] === false) {
         $_SESSION['error'] = 'Las contraseñas no coinciden.';
         $_SESSION['userIncorrecto'] = $valores['login'];
         header('Location: crear_cuenta.php');
       }else{
         insertarUsuario($pdo,$valores);
         $_SESSION['login'] = "Su cuenta ha sido creada, pruebe a iniciar sesión !!";
         header('Location: login.php');
      }
     }
   } catch (EmptyParamException|ValidationException $e){
       //No hago nada
   } catch (ParamException $e){
        header('Location: crear_cuenta.php');
   }

  mostrarCrearCuenta($valores, $error);

  piePagina();
  politicaCookies('../crear_cuenta.php')?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
