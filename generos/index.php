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
            $buscarGenero = isset($_GET['buscarGenero'])
                            ? trim($_GET['buscarGenero'])
                            : '';
            $st = $pdo->prepare('SELECT *
                                FROM generos
                                WHERE position(lower(:genero) in lower(genero)) != 0'); //position es como mb_substrpos() de php, devuelve 0
                                                                                        //si no encuentra nada. ponemos lower() de postgre para
                                                                                        //que no distinga entre mayu y minus
            //En execute(:titulo => "$valor"), indicamos lo que vale nuestros marcadores de prepare(:titulo)
            $st->execute([':genero' => "$buscarGenero"]);
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
          <div class="row">
            <div class="col-md-4">
              <table class="table table-bordered table-hover table-striped">
                  <thead>
                      <th>Género</th>
                      <th>Acciones</th>
                  </thead>
                  <tbody>
                      <?php while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
                                                              tb devuelve la fila, si la hay, por lo que entra,cuando no hay mas filas, da false y se sale.-->
                      <tr>
                          <td><?= h($fila['genero']) ?></td>
                          <td><a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                                 class="btn btn-xs btn-danger">
                                 Borrar
                               </a>
                               <a href="modificar.php?id=<?= $fila['id'] ?>"
                                      class="btn btn-xs btn-primary">
                                      Modificar
                                    </a>
                          </td>
                          <!--Al ser un enlace, la peticion es GET, por lo que le pasamos el id de la pelicula por la misma URL -->
                      </tr>
                      <?php endwhile ?>
                </tbody>
              </table>
            </div>
        </div>
        <div class="row">
            <div class="text-center">
                <a href="insertar.php" class="btn btn-info">Insertar un nuevo género</a>
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
            <?php $_SESSION['pagina'] = './generos/index.php'; ?>
              <a href="../crear_cookie.php" class="btn btn-success">Aceptar Cookies</a>
          </p>
      </div>
      </nav>
      <?php endif; ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
       <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
