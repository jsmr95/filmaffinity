<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menú</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <title></title>
  </head>
  <body>
    <?php
    require './comunes/auxiliar.php';
    navegadorInicio();
    ?>
<div class="container">
  <br>
  <?= compruebaSession('login','info'); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="center-block">
        <div class="panel panel-success">
          <div class="panel-heading text-center"> FILMAFFINITY </div>
          <div class="panel-body">
            <pre> <h3><p align="center">Bienvenidos a la página Oficial de Film-Affinity.</p></h3>

Podrás navegar por las diferentes opciones del navegador! Espero que encuentres la película que buscas!
          </pre>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
piePagina();
//MUESTRO LA NAV SI NO EXISTE LA COOKIE
if (!isset($_COOKIE['acepta'])): ?>
<nav class="navbar navbar-default navbar-fixed-bottom navbar-inverse">
<div class="container">
    <p class="navbar-text">Tienes que aceptar las politicas de cookies.</p>
    <p class="navbar-text navbar-right">
      <?php $_SESSION['pagina'] = 'index.php'; ?>
        <a href="crear_cookie.php" class="btn btn-success">Aceptar Cookies</a>
    </p>
</div>
</nav>
<?php endif; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
