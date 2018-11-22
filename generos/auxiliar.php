<?php

/**
 * Constante de generos
 * @param   array PAR1 array constante de generos
 */
const PAR1 = [
    'genero' => '',
];

/**
 * Busca un genero por un id
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   int $id ID del genero que quiere buscar
 * @return array|bool Devuelve la fila del genero o false si no existe
 */
function buscarGenero($pdo, $id)
{
  $st = $pdo->prepare('SELECT * FROM generos WHERE id = :id');
  $st->execute([':id' => $id]);
  return $st->fetch();
}

/**
 * Comprobar si un genero existe
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   int $id ID del genero que quiere buscar
 * @return array|bool Devuelve la fila del genero o false si no existe y aparte lanza Exception
 */
function comprobarGeneroExiste($pdo, $id)
{
    $fila = buscarGenero($pdo, $id);
    if ($fila === false) {
        throw new ParamException();
    }
    return $fila;
}

/**
 * Compruebo si el genero esta siendo usado por una pelicula
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   int $id ID del genero que quiere buscar
 * @return array|bool Devuelve la fila del genero o false si no esta siendo usado
 */
function compruebaGeneroEnUso($pdo, $id)
{
  $st = $pdo->prepare('SELECT * from peliculas WHERE genero_id = :id;');
  $st->execute([':id' => $id]);
  return $st->fetch();
}

/**
 * Saca los generos buscados para luego mostrarlos
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   string $buscarGenero valor a buscar dentro de generos
 * @return PDOStatement Devuelve el resultado de la sentencia o false
 */
function sacaGeneros($pdo, $buscarGenero)
{
  $st = $pdo->prepare('SELECT *
                      FROM generos
                      WHERE position(lower(:genero) in lower(genero)) != 0'); //position es como mb_substrpos() de php, devuelve 0
                                                                              //si no encuentra nada. ponemos lower() de postgre para
                                                                              //que no distinga entre mayu y minus
  //En execute(:titulo => "$valor"), indicamos lo que vale nuestros marcadores de prepare(:titulo)
  $st->execute([':genero' => "$buscarGenero"]);
  return $st;
}

/**
 * Muestra los generos
 * @param   PDOStatement $st Resultado de la sentencia para mostrar los generos
 */
function mostrarGeneros($st)
{
  ?>
  <div class="row">
    <div class="col-md-4">
      <table class="table table-bordered table-hover table-striped">
          <thead>
              <th>Género</th>
              <th>Acciones</th>
          </thead>
          <tbody>
              <?php if ($st !== false) {
                while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
                                                        tb devuelve la fila, si la hay, por lo que entra,cuando no hay mas filas, da false y se sale.-->
                <tr>
                    <td><?= h($fila['genero']) ?></td>
                    <td><a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                           class="btn btn-xs btn-danger">
                           Borrar
                         </a>
                         <a href="modificar.php?id=<?= $fila['id'] ?>"
                                class="btn btn-xs btn-primary">
                                Modificar
                              </a>
                    </td>
                    <!--Al ser un enlace, la peticion es GET, por lo que le pasamos el id de la pelicula por la misma URL -->
                </tr>
              <?php endwhile;
              } ?>
        </tbody>
      </table>
    </div>
</div> <?php
}

/**
 * Formulario para insertar o modificar
 * @param   array $valores valores para mantener en el modificar
 * @param   array $error array para añadir error si hiciera falta
 * @param   string $accion Accion que vamos a realizar, modificar o insertar
 */
function mostrarFormularioGenero($valores, $error, $accion)
{

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

/**
 * Insertar un genero
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   array $fila valores del genero a insertar
 */
function insertarGenero($pdo, $fila)
{
  $st = $pdo->prepare('INSERT INTO generos (genero)
  VALUES (:genero)');
  $st->execute($fila);
}

/**
 * Comprueba si el genero esta en la bd ya o no y comprueba si por POST se ha
 * introducido y le aplica restricciones
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   array $error array para añadir error si hiciera falta
 * @return array $fltGenero devuelve la fila del genero si existe
 */
function comprobarGenero($pdo, &$error)
{
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

/**
 * Modifica un genero
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   int $id ID del genero que quiere modificar
 * @param   array $fila fila del genero que quiere modificar
 */
function modificarGenero($pdo, $fila, $id)
{
    $st = $pdo->prepare('UPDATE generos
                            SET genero = :genero
                            WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}
