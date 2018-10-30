<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body>
        <?php
        $buscarTitulo = isset($_GET['buscarTitulo'])
                        ? trim($_GET['buscarTitulo'])
                        : '';
        $pdo = new PDO('pgsql:host=localhost;dbname=fa','fa','fa');
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
