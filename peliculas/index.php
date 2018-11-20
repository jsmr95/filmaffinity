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

        $st = buscarPeliculasBuscadores($pdo,$buscador, $buscar, $error);

        ?>
      </div>

      <div class="row form-inline" id="busqueda">
        <fieldset>
          <legend>Buscar</legend>
          <!-- Creamos un buscador de peliculas por titulo-->
          <form action="" method="get" class="form-inline">
            <div class="col-md-3">
              <div class="panel panel-default" id="fondoTabla">
                <div class="panel-body">
                  <div class="form-group <?= hasError($buscador, $error) ?>">
                    <label for="buscar">Buscar por <?= opcionesBuscar($buscador) ?>:</label>
                    <input id="buscar" type="text" name="buscar"
                    value="<?= $buscar ?>" class="form-control">
                    <?php mensajeError($buscador, $error) ?>
                  </div>
                </div>
              </div>
              <input type="submit" value="Buscar" class="btn btn-primary">
            </div>
          </form>
        </fieldset>
      </div>
  <hr>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-bordered table-hover table-striped">
              <thead>
                  <th>Título</th>
                  <th>Año</th>
                  <th>Sinopsis</th>
                  <th>Duración</th>
                  <th>Género</th>
                  <th>Acciones</th>
              </thead>
              <tbody>
                  <?php
                  if ($st !== false) {
                    while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
                                                            tb devuelve la fila, si la hay, por lo que entra,cuando no hay mas filas, da false y se sale.-->
                    <tr>
                        <td><?= h($fila['titulo']) ?></td>
                        <td><?= h($fila['anyo']) ?></td>
                        <td><?= h($fila['sinopsis']) ?></td>
                        <td><?= h($fila['duracion']) ?></td>
                        <td><?= h($fila['genero']) ?></td>
                        <!--Al ser un enlace, la peticion es GET, por lo que le pasamos el id de la pelicula por la misma URL -->
                        <td><a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                               class="btn btn-xs btn-danger">
                               Borrar
                             </a>
                             <a href="modificar.php?id=<?= $fila['id'] ?>"
                               class="btn btn-xs btn-info">
                               Modificar
                             </a>
                        </td>
                    </tr>
                  <?php endwhile;
                }  ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="text-center">
          <a href="insertar.php" class="btn btn-info">Insertar una nueva película</a>
        </div>
      </div>
    </div>
    <?php
    piePagina();
    //MUESTRO LA NAV SI NO EXISTE LA COOKIE
    politicaCookies('../peliculas/index.php') ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
  </body>
</html>
