<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Bases de datos</title>
    </head>
    <body>
        <?php
            $pdo = new PDO('pgsql:host=localhost;dbname=fa','fa','fa');
            $numFilas = $pdo->exec("INSERT INTO generos (genero)
                                    VALUES ('Costumbrismo')");
            $st = $pdo->query('SELECT * from generos');
            // $res = $st->fetchAll(); el st (PDOStatement) tb se puede recorrer
         ?>
    <table border="1" style="margin:auto"><!--El style lo centra-->
        <thead>
            <th>Id</th>
            <th>Género</th>
        </thead>
        <tbody>
            <?php while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
                                                    tb devuelve la fila, si la hay, por lo que entra,cuando no hay mas filas, da false y se sale.-->
            <tr>
                <td><?= $fila['id'] ?></td>
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
    </body>
</html>
