<?php session_start();
require './comunes/auxiliar.php';
navegadorInicio();
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Iniciar sesión</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    </head>
    <body>
        <?php
        const PAR_LOGIN = ['login' => '', 'password' => ''];
        $valores = PAR_LOGIN;

        try{
           $error = [];
           $pdo = conectar();
           comprobarParametros(PAR_LOGIN);
           $valores = array_map('trim', $_POST);

           $flt['login'] = comprobarLogin($error);
           $flt['password'] = comprobarPassword($error);
           comprobarUsuario($flt,$pdo,$error);
           comprobarErrores($error);
           //Queda logearse
           header('Location: index.php');
       } catch (EmptyParamException|ValidationException $e){
           //No hago nada
       } catch (ParamException $e){
           header('Location: index.php');
       }
         ?>
         <br><br>
      <div class="container">
          <div class="row">
            <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Iniar sesión</h3>
                </div>
                <div class="panel-body">
                  <form class="" action="" method="post">
                    <div class="form-group">
                      <label for="login">Usuario:</label>
                      <input type="text" class="form-control" name="login" value="">
                    </div>
                    <div class="form-group">
                      <label for="password">Password:</label>
                      <input type="password" class="form-control" name="password" value="">
                    </div>
                    <button type="submit" class="btn btn-default">Iniciar sesión </button>
                  </form>
                </div>
                </div>
            </div>
          </div>
      </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
