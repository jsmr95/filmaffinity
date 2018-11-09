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
    navegador();
    ?>
<div class="container">
  <br>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
