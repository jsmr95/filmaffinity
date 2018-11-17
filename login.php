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
      <title>Iniciar sesión</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  </head>
  <body>
    <br><br>
    <div class="container">
      <?php
      compruebaSession('sesion', 'danger');
      compruebaSession('login', 'info');

      $valores = PAR_LOGIN;
      //Compruebo si ha fallado en el login o contraseña para msotrarlos
      if (isset($_SESSION['userIncorrecto']) || isset($_SESSION['passIncorrecta'])) {
        $valores['login'] = $_SESSION['userIncorrecto'];
        $valores['password'] = $_SESSION['passIncorrecta'];
        $_SESSION['userIncorrecto'] = '';
        $_SESSION['passIncorrecta'] = '';
      }

      try{
         $error = [];
         $pdo = conectar();
         comprobarParametros(PAR_LOGIN);
         $valores = array_map('trim', $_POST);
         $valores['login'] = comprobarLogin($error);
         $valores['password'] = comprobarPassword($error);
         $usuario = comprobarUsuario($valores,$pdo,$error);
         comprobarErrores($error);
         if ($usuario === false) {
           $_SESSION['sesion'] = 'El usuario o la contraseña son incorrectos.';
           //Guardo las incorrectas para que se mantenga
           $_SESSION['userIncorrecto'] = $valores['login'];
           $_SESSION['passIncorrecta'] = $valores['password'];
           header('Location: login.php');
         }else {
           $_SESSION['usuario'] = $usuario['login'];
           $_SESSION['login'] = "Bienvenido a film-affinity $_SESSION[usuario] !!";
           irAlIndice();
       }
     } catch (EmptyParamException|ValidationException $e){
         //No hago nada
     } catch (ParamException $e){
         irAlIndice();
     }
       ?>
     <div class="row">
       <div class="col-md-4">
       <div class="panel panel-primary">
           <div class="panel-heading">
               <h3 class="panel-title">Iniar sesión</h3>
           </div>
           <div class="panel-body">
             <form class="" action="" method="post">
                 <div class="form-group <?= hasError('login', $error) ?>">
                     <label for="login" class="control-label">Usuario</label>
                     <input id="login" type="text" name="login"
                            class="form-control" value="<?=$valores['login']?>">
                     <?php mensajeError('login', $error) ?>
                 </div>
                 <div class="form-group <?= hasError('password', $error) ?>">
                     <label for="password" class="control-label">Contraseña:</label>
                     <input id="password" type="password" name="password"
                            class="form-control" value="<?=$valores['password']?>">
                     <?php mensajeError('password', $error) ?>
                 </div>
              <button type="submit" class="btn btn-default">Iniciar sesión </button>
              <a href="crear_cuenta.php" class="btn btn-default">Crear cuenta </a>
            </form>
          </div>
          </div>
       </div>
     </div>
    </div>
    <?php piePagina(); ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
