<?php
    function conectar()
    {
        return new PDO('pgsql:host=localhost;dbname=fa','fa','fa');
    }

    function buscarPelicula($pdo, $id)
    {
        $st = $pdo->prepare('SELECT id from peliculas WHERE id = :id');
        //si no hay alguna fila que cumple con el id, te manda a la misma pagina
        $st->execute([':id' => $id]);
        //Te devuelve la pelicula, si no esta, devuelve FALSE
        return $st->fetch();
    }
