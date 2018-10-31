<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Insetar una nueva pelicula</title>
    </head>
    <body>
        <h3>Insertar una nueva pelicula</h3>
        <form action="" method="post">
            <label for="titulo">Titulo</label>
            <input type="text" name="titulo" ><br />
            <label for="anyo">Año</label>
            <input type="text" name="anyo" ><br />
            <label for="sinopsis">Sinopsis</label>
            <textarea name="sinopsis" rows="8" cols="80"></textarea><br />
            <label for="duracion">Duración</label>
            <input type="text" name="duracion" ><br />
            <label for="genero_id">Género</label>
            <input type="text" name="genero_id" ><br />
            <input type="submit" value="Insertar">
            <a href="index.php">Volver</a>
        </form>
    </body>
</html>
