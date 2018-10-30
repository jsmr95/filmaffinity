<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Confirmar borrado</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            header('Location: index.php'); //genero una respuesta HTTP 302(Redireccionamiento) con la función header
        }
        $pdo = conectar();
        //si no hay alguna fila que cumple con el id, te manda a la misma pagina
        if (!buscarPelicula($pdo, $id)) {
            header('Location: index.php');
        }
        ?>
        <h3>¿Seguro que deseas borrar la fila ?</h3>
        <form action="index.php" method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="submit" value="Si">
            <a href="index.php">No</a>
        </form>
    </body>
</html>
