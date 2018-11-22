<?php session_start();
require '../comunes/auxiliar.php';
require './auxiliar.php';
navegador();
?>
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
            irAlIndice();
        } catch (EmptyParamException|ValidationException $e){
            //No hago nada
        } catch (ParamException $e){
          $_SESSION['error'] = 'El género no ha sido insertado.';
            irAlIndice();
        }
        mostrarFormularioGenero($valores ,$error, 'Insertar');
        
        piePagina();
        politicaCookies('../generos/insertar.php')?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
