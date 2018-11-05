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

    function comprobarTitulo(&$error)
    {
        $fltTitulo = trim(filter_input(INPUT_POST, 'titulo'));
        if (mb_strlen($fltTitulo) > 255) {
            $error[] = 'El titulo es demasiado largo.';
        }
        return $fltTitulo;
    }

    function comprobarAnyo(&$error)
    {
        $fltAnyo = filter_input(INPUT_POST,'anyo',FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999], ]);
        if ($fltAnyo === false) {
            $error[] = 'El año no es correcto.';
        }
        return $fltAnyo;
    }

    function comprobarDuracion(&$error)
    {
        $fltDuracion = trim(filter_input(INPUT_POST, 'duracion'));
        if (mb_strlen($fltDuracion) !== '') {
            $fltDuracion = filter_input(INPUT_POST, 'duracion', FILTER_VALIDATE_INT, ['options' => [
                'min_range' => 0,
                'max_range' => 32767,
            ],
        ]);
            if ($fltDuracion === false) {
                $error[] = 'La duración no es correcta.';
            }
        } else {
        $fltDuracion = null;
        }
        return $fltDuracion;
    }
