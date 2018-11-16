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

            $buscarTitulo = isset($_GET['buscarTitulo'])
                            ? trim($_GET['buscarTitulo'])
                            : '';
            $st = $pdo->prepare('SELECT p.*, genero
                                FROM peliculas p
                                JOIN generos g
                                ON genero_id = g.id
                                WHERE position(lower(:titulo) in lower(titulo)) != 0'); //position es como mb_substrpos() de php, devuelve 0
                                                                                        //si no encuentra nada. ponemos lower() de postgre para
                                                                                        //que no distinga entre mayu y minus
            //En execute(:titulo => "$valor"), indicamos lo que vale nuestros marcadores de prepare(:titulo)
            $st->execute([':titulo' => "$buscarTitulo"]);
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
                      <div class="form-group">
                        <label for="buscarTitulo">Buscar por título:</label>
                        <input id="buscarTitulo" type="text" name="buscarTitulo"
                        value="<?= $buscarTitulo ?>" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
              <!-- Creamos un buscador de peliculas por año-->
              <div class="panel panel-default" id="fondoTabla">
                <div class="panel-body">
                  <div class="form-group">
                    <label for="buscarAnyo">Buscar por año:</label>
                    <input id="buscarAnyo" type="text" name="buscarAnyo"
                    value="<?= $buscarAnyo ?>" class="form-control">
                  </div>
                </div>
              </div>
            </div>
              <div class="col-md-3">
                <!-- Creamos un buscador de peliculas por duración-->
                <div class="panel panel-default" id="fondoTabla">
                  <div class="panel-body">
                    <div class="form-group">
                      <label for="buscarDuracion">Buscar por duración:</label>
                      <input id="buscarDuracion" type="text" name="buscarDuracion"
                      value="<?= $buscarDuracion ?>" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <!-- Creamos un buscador de peliculas por género-->
                <div class="panel panel-default" id="fondoTabla">
                  <div class="panel-body">
                    <div class="form-group">
                      <label for="buscarGenero">Buscar por género:</label>
                      <input id="buscarGenero" type="text" name="buscarGenero"
                      value="<?= $buscarGenero ?>" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
                <input type="submit" value="Buscar" class="btn btn-primary">
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
                      <?php while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
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
                      <?php endwhile ?>
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
      //MUESTRO LA NAV SI NO EXISTE LA COOKIE
      if (!isset($_COOKIE['acepta'])): ?>
      <nav class="navbar navbar-default navbar-fixed-bottom navbar-inverse">
      <div class="container">
          <p class="navbar-text">Tienes que aceptar las politicas de cookies.</p>
          <p class="navbar-text navbar-right">
            <?php $_SESSION['pagina'] = './peliculas/index.php'; ?>
              <a href="../crear_cookie.php" class="btn btn-success">Aceptar Cookies</a>
          </p>
      </div>
      </nav>
      <?php endif;
      piePagina(); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
