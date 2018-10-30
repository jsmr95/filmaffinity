<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        $pdo = conectar();
        //Pregunto si vengo del confirm_borrado, si existe un id por POST, es que quiero borrar una fila
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $pdo->beginTransaction();
            $pdo->exec('LOCK TABLE peliculas IN SHARE MODE');
            if (!buscarPelicula($pdo, $id)) { ?>
                <h3>Error: La pelicula no existe!</h3>
            <?php
            } else {
                $st = $pdo->prepare('DELETE FROM peliculas WHERE id = :id');
                $st->execute([':id' => $id]); ?>
                <h3>Película borrada correctamente.</h3>
        <?php
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
        <div>
          <!-- Creamos un buscador de peliculas -->
            <fieldset>
                <legend>Buscar</legend>
                <form action="" method="get">
                    <label for="buscarTitulo">Buscar por título:</label>
                    <input id="buscarTitulo" type="text" name="buscarTitulo"
                    value="<?= $buscarTitulo ?>">
                    <input type="submit" value="Buscar">
                </form>
            </fieldset>
        </div>

    <div style="margin-top: 20px">
        <table border="1" style="margin:auto"><!--El style lo centra-->
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
                    <td><?= $fila['titulo'] ?></td>
                    <td><?= $fila['anyo'] ?></td>
                    <td><?= $fila['sinopsis'] ?></td>
                    <td><?= $fila['duracion'] ?></td>
                    <td><?= $fila['genero'] ?></td>
                    <td><a href="confirm_borrado.php?id=<?= $fila['id'] ?>">Borrar</a></td>
                    <!--Al ser un enlace, la peticion es GET, por lo que le pasamos el id de la pelicula por la misma URL -->
                </tr>
                <?php endwhile ?>



                <!-- <?php foreach ($st as $fila):?>
                <tr>
                    <td><?= $fila['id'] ?></td>
                    <td><?= $fila['genero'] ?></td>
                </tr>
                <?php endforeach ?> -->

                <!-- VALE CON EL FOREACH O CON EL WHILE, LAS DOS FORMAS SON OK!!-->
            </tbody>
        </table>
    </div>
    </body>
</html>
