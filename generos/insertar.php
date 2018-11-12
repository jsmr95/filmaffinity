<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Insertar una nueva película</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
      <?php
      require '../comunes/auxiliar.php';
      require './auxiliar.php';

      $valores = PAR1;
         try{
            $error = [];
            comprobarParametros(PAR1);
            extract(array_map('trim', $_POST), EXTR_IF_EXISTS);
            $pdo = conectar();
            $flt['genero'] = comprobarGenero($pdo, $error);
            comprobarErrores($error);
            insertarGenero($pdo, $flt);
            $_SESSION['mensaje'] = 'El género ha sido insertado correctamente.';
            header('Location: index.php');
        } catch (EmptyParamException|ValidationException $e){
            //No hago nada
        } catch (ParamException $e){
          $_SESSION['error'] = 'El género no ha sido insertado.';
            header('Location: index.php');
        }
        ?>
        <br>
        <div class="container">
          <?php mostrarFormularioGenero($valores ,$error, 'Insertar'); ?>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
