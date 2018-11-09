<?php

const PAR1 = [
    'genero' => '',
];

function comprobarGeneroExiste($pdo, $id){
    $fila = buscarGenero($pdo, $id);
    if ($fila === false) {
        throw new ParamException();
    }
    return $fila;
}

function compruebaGeneroEnUso($pdo, $id){
  $st = $pdo->prepare('SELECT * from peliculas WHERE genero_id = :id;');
  $st->execute([':id' => $id]);
  return $st->fetch();
}

function mostrarFormularioGenero($valores, $error, $accion){

  extract($valores);
  ?>
  <div class="panel panel-primary">
      <div class="panel-heading">
          <h3 class="panel-title"><?= $accion ?> un Género...</h3>
      </div>
      <div class="panel-body">
          <form action="" method="post">
              <div class="form-group <?= hasError('genero', $error) ?>">
                  <label for="titulo" class="control-label">Género</label>
                  <input type="text" name="genero" class="form-control" id="genero" value="<?= h($genero) ?>" >
                  <?php mensajeError('genero', $error) ?>
              </div>
              <input type="submit" value="<?= $accion ?>" class="btn btn-success">
              <a href="index.php" class="btn btn-info">Volver</a>
          </form>
      </div>
  </div>
  <?php
}

function insertarGenero($pdo, $fila){
  $st = $pdo->prepare('INSERT INTO generos (genero)
  VALUES (:genero)');
  $st->execute($fila);
}

function comprobarGenero($pdo, &$error){
  $fltGenero = filter_input(INPUT_POST, 'genero');
  if ($fltGenero === '') {
      $error ['genero'] = 'El género es obligatorio.';
  } elseif (mb_strlen($fltGenero) > 255) {
      $error['genero'] = 'El género es demasiado largo.';
  }else {
  $st = $pdo->prepare('SELECT * FROM generos WHERE lower(genero) = lower(:genero)');
  $st->execute([':genero' => $fltGenero]);
  if ($st->fetch()) {
    $error['genero'] = 'Ese género ya existe, y los géneros son únicos.';
  }
}
  return $fltGenero;
}

function buscarGenero($pdo, $id){
  $st = $pdo->prepare('SELECT * FROM generos WHERE id = :id');
  $st->execute([':id' => $id]);
  return $st->fetch();
}

function modificarGenero($pdo, $fila, $id){
    $st = $pdo->prepare('UPDATE generos
                            SET genero = :genero
                            WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}
