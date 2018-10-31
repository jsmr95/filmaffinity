<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Insetar una nueva pelicula</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        const PAR = [
            'titulo' => '',
            'anyo' => '',
            'sinopsis' => '',
            'duracion' => '',
            'genero_id' => '',
        ];
        $pdo = conectar();

        extract(PAR);

        if (isset($_POST['titulo'], $_POST['anyo'], $_POST['sinopsis'],
                  $_POST['duracion'], $_POST['genero_id'])) {
           extract(array_map('trim', $_POST), EXTR_IF_EXISTS);
        }

        ?>
        <h3>Insertar una nueva pelicula</h3>
        <form action="" method="post">
            <label for="titulo">Titulo</label>
            <input type="text" name="titulo" id="titulo" value="<?= $titulo ?>"><br />
            <label for="anyo">Año</label>
            <input type="text" name="anyo" id="anyo" value="<?= $anyo ?>"><br />
            <label for="sinopsis">Sinopsis</label>
            <textarea name="sinopsis" rows="8" cols="80" id="sinopsis"><?= $sinopsis ?></textarea><br />
            <label for="duracion">Duración</label>
            <input type="text" name="duracion" id="duracion" value="<?= $duracion ?>"><br />
            <label for="genero_id">Género</label>
            <input type="text" name="genero_id" id="genero_id" value="<?= $genero_id ?>"><br />
            <input type="submit" value="Insertar">
            <a href="index.php">Volver</a>
        </form>
    </body>
</html>
