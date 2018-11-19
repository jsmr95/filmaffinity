<?php

const PAR = [
    'titulo' => '',
    'anyo' => '',
    'sinopsis' => '',
    'duracion' => '',
    'genero_id' => '',
];

const BUSCADORES = ['título','año','duración','género'];

function existe($buscador){
  return isset($_GET[$buscador]) ? trim($_GET[$buscador]) : '';
}

function opcionesBuscar($buscador)
{
  ?>
  <select name='buscador'><?php
    foreach (BUSCADORES as $busca):
      ?>
        <option value="<?= $busca ?>"  <?= buscadorSeleccionado($buscador,$busca)?>>
            <?= $busca ?>
        </option>
      <?php
    endforeach;
    ?>
  </select><?php
}

function buscarPeliculasBuscadores($pdo,$buscador, $buscar,&$error)
{
  if ($buscador == 'título' || $buscador == '') {
    $st = $pdo->prepare('SELECT p.*, genero
                        FROM peliculas p
                        JOIN generos g
                        ON genero_id = g.id
                        WHERE position(lower(:titulo) in lower(titulo)) != 0 '); //position es como mb_substrpos() de php, devuelve 0
                                                                                //si no encuentra nada. ponemos lower() de postgre para
                                                                                //que no distinga entre mayu y minus
    //En execute(:titulo => "$valor"), indicamos lo que vale nuestros marcadores de prepare(:titulo)
    $st->execute([':titulo' => "$buscar"]);
    return $st;

  }elseif ($buscador == 'género') {
    $st = $pdo->prepare('SELECT p.*, genero
                        FROM peliculas p
                        JOIN generos g
                        ON genero_id = g.id
                        WHERE position(lower(:genero) in lower(genero)) != 0 ');
    $st->execute([':genero' => "$buscar"]);
    return $st;
  }

  elseif ($buscador == 'duración') {
    if ($buscar == '') {
      $error['duración'] = 'La duración no puede estar vacía.';
      return false;
    }else {
    $st = $pdo->prepare('SELECT p.*, genero
                        FROM peliculas p
                        JOIN generos g
                        ON genero_id = g.id
                        WHERE :duracion = duracion');

    $st->execute([':duracion' => "$buscar"]);
    return $st;
    }

  } elseif ($buscador == 'año') {
      if ($buscar == '') {
        $error['año'] = 'El año no puede estar vacío.';
        return false;
      }elseif ($buscar <1000 || $buscar > 9999) {
        $error['año'] = 'El año debe ser de 4 números.';
        return false;
      }else {
    $st = $pdo->prepare('SELECT p.*, genero
                        FROM peliculas p
                        JOIN generos g
                        ON genero_id = g.id
                        WHERE :anyo = anyo ');

    $st->execute([':anyo' => "$buscar"]);
    return $st;
      }
    }
}

function buscarPelicula($pdo, $id)
{
    $st = $pdo->prepare('SELECT * from peliculas WHERE id = :id');
    //si no hay alguna fila que cumple con el id, te manda a la misma pagina
    $st->execute([':id' => $id]);
    //Te devuelve la pelicula, si no esta, devuelve FALSE
    return $st->fetch();
}

function comprobarTitulo(&$error)
{
    $fltTitulo = trim(filter_input(INPUT_POST, 'titulo'));
    if ($fltTitulo === '') {
        $error ['titulo'] = 'El titulo es obligatorio.';
    } elseif (mb_strlen($fltTitulo) > 255) {
        $error['titulo'] = 'El titulo es demasiado largo.';
    }
    return $fltTitulo;
}

function comprobarAnyo(&$error)
{
    $fltAnyo = filter_input(INPUT_POST,'anyo',FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999], ]);
    if ($fltAnyo === false) {
        $error['anyo'] = 'El año no es correcto.';
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
            $error['duracion'] = 'La duración no es correcta.';
        }
    } else {
    $fltDuracion = null;
    }
    return $fltDuracion;
}

function comprobarGeneroId($pdo, &$error)
{
    $fltGeneroId = filter_input(INPUT_POST, 'genero_id',FILTER_VALIDATE_INT);
    if ($fltGeneroId !== false)
    {
        $st = $pdo->prepare('SELECT id from generos WHERE id = :id');
        $st-> execute([':id' => $fltGeneroId]);
        if ($st->fetch() === false){
            $error ['genero_id'] = 'No existe ese género.';
        }
    } else {
        $error ['genero_id'] = 'El género no es correcto.';
    }
    return $fltGeneroId;
}

function insertarPelicula($pdo, $fila)
{
    $st = $pdo->prepare('INSERT INTO peliculas (titulo, anyo, sinopsis, duracion, genero_id)
    VALUES (:titulo, :anyo, :sinopsis, :duracion, :genero_id)');
    $st->execute($fila);
}

function modificarPelicula($pdo, $fila, $id){
    $st = $pdo->prepare('UPDATE peliculas
                            SET titulo = :titulo
                                , anyo = :anyo
                                , sinopsis = :sinopsis
                                , duracion = :duracion
                                , genero_id = :genero_id
                            WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}

function mostrarFormulario($valores, $error, $pdo, $accion)
{
    extract($valores);
    $st = $pdo->query('SELECT * FROM generos');
    $generos = $st->fetchAll();

    ?>
    <br>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><?= $accion ?> una nueva película...</h3>
        </div>
        <div class="panel-body">
            <form action="" method="post">
                <div class="form-group <?= hasError('titulo', $error) ?>">
                    <label for="titulo" class="control-label">Título</label>
                    <input id="titulo" type="text" name="titulo"
                           class="form-control" value="<?= h($titulo) ?>">
                    <?php mensajeError('titulo', $error) ?>
                </div>
                <div class="form-group <?= hasError('anyo', $error) ?>">
                    <label for="anyo" class="control-label">Año</label>
                    <input id="anyo" type="text" name="anyo"
                           class="form-control" value="<?= h($anyo) ?>">
                    <?php mensajeError('anyo', $error) ?>
                </div>
                <div class="form-group">
                    <label for="sinopsis" class="control-label">Sinopsis</label>
                    <textarea id="sinopsis"
                              name="sinopsis"
                              rows="8"
                              cols="80"
                              class="form-control"><?= h($sinopsis) ?></textarea>
                </div>
                <div class="form-group <?= hasError('duracion', $error) ?>">
                    <label for="duracion" class="control-label">Duración</label>
                    <input id="duracion" type="text" name="duracion"
                           class="form-control"
                           value="<?= h($duracion) ?>">
                    <?php mensajeError('duracion', $error) ?>
                </div>
                <div class="form-group <?= hasError('genero_id', $error) ?>">
                    <label for="genero_id" class="control-label">Género</label>
                    <select id="genero_id" class="form-control" name="genero_id">

                        <?php
                        foreach ($generos as $genero):
                          ?>
                            <option value="<?= $genero['id'] ?>" <?= generoSeleccionado($genero['id'], $genero_id) ?>>
                                <?= $genero['genero'] ?>
                            </option>
                          <?php
                        endforeach ?>
                    </select>
                    <?php mensajeError('genero_id', $error) ?>
                </div>
                <input type="submit" value="<?= h($accion) ?>"
                       class="btn btn-success">
                <a href="index.php" class="btn btn-info">Volver</a>
            </form>
        </div>
    </div>
    <?php
}

function comprobarPelicula($pdo, $id){
    $fila = buscarPelicula($pdo, $id);
    if ($fila === false) {
        throw new ParamException();
    }
    return $fila;
}

function generoSeleccionado($genero, $genero_id)
{
  return $genero == $genero_id ? "selected" : "";
}

function buscadorSeleccionado($buscador, $busca)
{
  return $buscador == $busca ? "selected" : "";
}
