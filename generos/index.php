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
         <div class="row">
           <?php
           compruebaSession('mensaje', 'success');
           compruebaSession('error', 'danger');

            $pdo = conectar();
            //Pregunto si vengo del confirm_borrado, si existe un id por POST, es que quiero borrar una fila
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $pdo->beginTransaction();
                $pdo->exec('LOCK TABLE peliculas IN SHARE MODE');
                if (!buscarGenero($pdo, $id)) {
                  $_SESSION['error'] = 'El género no existe.';
                  irAlIndice();
              } elseif (compruebaGeneroEnUso($pdo, $id)) {
                $_SESSION['error'] = 'El género está usandose por una pelicula, no se puede borrar un género en uso!';
                irAlIndice();
              } else {
                    $st = $pdo->prepare('DELETE FROM generos WHERE id = :id');
                    $st->execute([':id' => $id]);
                    if (buscarGenero($pdo, $id) === false) {
                      $_SESSION['mensaje'] = 'El género ha sido borrado correctamente.';
                      irAlIndice();
                    }
                }
                $pdo->commit();
            }
            $buscarGenero = existe('buscarGenero');
            $st = sacaGeneros($pdo, $buscarGenero);
            ?>
          </div>
        <div class="row" id="busqueda">
          <div class="col-md-12">
            <!-- Creamos un buscador de peliculas por Genero-->
              <fieldset>
                <legend>Buscar</legend>
                <form action="" method="get" class="form-inline">
                  <div class="form-group">
                    <label for="buscarGenero">Buscar por género:</label>
                    <input id="buscarGenero" type="text" name="buscarGenero"
                    value="<?= $buscarGenero ?>" class="form-control">
                  </div>
                  <input type="submit" value="Buscar" class="btn btn-primary">
                </form>
              </fieldset>
            </div>
          </div>
          <hr>
          <?= mostrarGeneros($st); ?>
        <div class="row">
          <div class="text-center">
            <a href="insertar.php" class="btn btn-info">Insertar un nuevo género</a>
          </div>
        </div>
      </div>
      <?php
      piePagina();
      //MUESTRO LA NAV SI NO EXISTE LA COOKIE
      politicaCookies('../generos/index.php') ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
