<?php session_start() ?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
       <meta charset="utf-8">
       <meta http-equiv="X-UA-Compatible" content="IE=edge">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <title>Confirmar borrado</title>
       <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
        <?php
        require '../comunes/auxiliar.php';
        require './auxiliar.php';

        //El usuario debe estar logeado para poder borrar peliculas
        if (!isset($_SESSION['usuario'])) {
           $_SESSION['mensaje'] = 'Debe iniciar sesión para poder borrar películas';
           irAlIndice();
       } elseif ($_SESSION['usuario'] != 'admin') {
           $_SESSION['mensaje'] = 'Debe ser administrador para poder borrar películas';
           irAlIndice();
       }

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            irAlIndice(); //genero una respuesta HTTP 302(Redireccionamiento) con la función header
        }
        $pdo = conectar();
        //si no hay alguna fila que cumple con el id, te manda a la misma pagina
        if (!buscarGenero($pdo, $id)) {
            irAlIndice();
        }
        ?>
        <div class="container">
          <div class="row">
            <h3>¿Seguro que deseas borrar la fila ?</h3>
            <div class="col-mg-4">
              <form action="index.php" method="post" class="form-inline">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="submit" value="Si" class="form-control btn btn-danger">
                <a href="index.php" class="btn btn-success">No</a>
              </form>
            </div>
          </div>
        </div>
        <?php piePagina(); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
