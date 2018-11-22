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
    <title>FilmAffinity</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style media="screen">
        #busqueda { margin-top: 1em; }
    </style>
  </head>
  <body>
    <div class="container">
      <?php
      compruebaSession('mensaje', 'success');
      compruebaSession('error', 'danger');
      ?>
     <div class="row">
        <?php

        $pdo = conectar();
        //Pregunto si vengo del confirm_borrado, si existe un id por POST, es que quiero borrar una fila
        if (isset($_POST['id'])) {
          $id = $_POST['id'];
          $pdo->beginTransaction();
          $pdo->exec('LOCK TABLE peliculas IN SHARE MODE');
          if (!buscarPelicula($pdo, $id)) {
            $_SESSION['error'] = 'La película no existe.';
            irAlIndice();
          } else {
            $st = $pdo->prepare('DELETE FROM peliculas WHERE id = :id');
            $st->execute([':id' => $id]);
            if (buscarPelicula($pdo, $id) === false) {
              $_SESSION['mensaje'] = 'La película ha sido borrada correctamente.';
              irAlIndice();
            }
          }
          $pdo->commit();
        }
    $error = [];
    $buscar = existe('buscar');
    $buscador = existe('buscador');

    $st = sacaPeliculasBuscadores($pdo,$buscador, $buscar, $error);

    cuerpoPeliculas($error,$buscador,$buscar,$st);

    piePagina();
    //MUESTRO LA NAV SI NO EXISTE LA COOKIE
    politicaCookies('../peliculas/index.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
