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
        <title>Modificar género</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
      <?php

      compruebaLogueadoModificar('generos');

      try{
         $error = [];
         $id = comprobarId();
         $pdo = conectar();
         $fila = comprobarGeneroExiste($pdo, $id);
         comprobarParametros(PAR1);
         $valores = array_map('trim', $_POST);
         $flt['genero'] = comprobarGenero($pdo, $error);
         comprobarErrores($error);
         modificarGenero($pdo, $flt, $id);
         //Creamos el mensaje de modificacion en $_SESSION
         $_SESSION['mensaje'] = 'Género modificado correctamente.';
         irAlIndice();
     } catch (EmptyParamException|ValidationException $e){
         //No hago nada
     } catch (ParamException $e){
       $_SESSION['error'] = 'El género no ha sido modificado.';
       irAlIndice();
     }
    mostrarFormularioGenero($fila ,$error, 'Modificar');
    
    piePagina();
    politicaCookies('../generos/modificar.php')?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
