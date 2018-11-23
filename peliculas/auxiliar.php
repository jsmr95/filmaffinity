<?php

/**
 * CONSTANTES
 * @param   PAR constantes de los valores de una pelicula
 * @param   BUSCADORES constantes de los buscadores
 */
const PAR = [
    'titulo' => '',
    'anyo' => '',
    'sinopsis' => '',
    'duracion' => '',
    'genero_id' => '',
];

const BUSCADORES = ['título','año','duración','género'];

/**
 * Muestra las opciones por las que puedo buscar peliculas
 * @param   string $buscador valor del buscador seleccionado
 */
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

/**
 * Saca las peliculas segun los buscadores
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   string $valores por el que busca -> ej: titulo
 * @param   array $error array para meter errores
 */
function sacaPeliculasBuscadores($pdo,$valores,$error)
{
    extract($valores);
    $where = $execute = [];
    if (isset($_GET['buscarTitulo'])) {
        $buscarTitulo = trim($_GET['buscarTitulo']);
        if ($buscarTitulo !== '') {
            $where[] = 'titulo ILIKE :titulo';
            $execute[':titulo'] = "%$buscarTitulo%";
        }
    }
    if (isset($_GET['buscarAnyo'])) {
        $buscarAnyo = trim($_GET['buscarAnyo']);
        if ($buscarAnyo !== '') {
            $where[] = 'anyo::text = :anyo';
            $execute[':anyo'] = $buscarAnyo;
        }
    }
    if (isset($_GET['buscarDuracion'])) {
        $buscarDuracion = trim($_GET['buscarDuracion']);
        if ($buscarDuracion !== '') {
            $where[] = 'duracion::text = :duracion';
            $execute[':duracion'] = $buscarDuracion;
        }
    }
    if (isset($_GET['buscarGenero'])) {
        $buscarGenero = trim($_GET['buscarGenero']);
        if ($buscarGenero !== '') {
            $where[] = 'genero_id::text = :genero_id';
            $execute[':genero_id'] = $buscarGenero;
        }
    }
    $where = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
    $st = $pdo->prepare("SELECT p.*, genero
                           FROM peliculas p
                           JOIN generos g
                             ON genero_id = g.id
                             $where
                       ORDER BY id");
    $st->execute($execute);
    $generos = recogerGeneros($pdo);
    cuerpoPeliculas($error,$valores,$st,$generos);

}

/**
 * Busca una pelicula por un id
 * @param   PDO $pdo Conexion con la base de datos generos
 * @param   int $id ID de la pelicula que quiere buscar
 * @return array|bool Devuelve la fila de la pelicula o false si no existe
 */
function buscarPelicula($pdo, $id)
{
    $st = $pdo->prepare('SELECT * from peliculas WHERE id = :id');
    //si no hay alguna fila que cumple con el id, te manda a la misma pagina
    $st->execute([':id' => $id]);
    //Te devuelve la pelicula, si no esta, devuelve FALSE
    return $st->fetch();
}

/**
 * Aplica restricciones al titulo
 * @param   array $error array para meter errores
 * @return string devulve el titulo
 */
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

/**
 * Aplica restricciones a año
 * @param   array $error array de errores
 * @return array|bool devuelve el año o false
 */
function comprobarAnyo(&$error)
{
    $fltAnyo = filter_input(INPUT_POST,'anyo',FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999], ]);
    if ($fltAnyo === false) {
        $error['anyo'] = 'El año no es correcto.';
    }
    return $fltAnyo;
}

/**
 * Aplica restricciones a duracion
 * @param   array $error array de errores
 * @return array|bool devuelve la duracion o false
 */
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

/**
 * Aplica restricciones a genero_id
 * @param   PDO  $pdo conexion con la base de datos
 * @param   array $error array de errores
 * @return array|bool devuelve el genero_id o false
 */
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

/**
 * Inserta una pelicula
 * @param   PDO $pdo conexion con la base de datos
 * @param   array  $fila fila que se desea insertar en peliculas
 */
function insertarPelicula($pdo, $fila)
{
    $st = $pdo->prepare('INSERT INTO peliculas (titulo, anyo, sinopsis, duracion, genero_id)
    VALUES (:titulo, :anyo, :sinopsis, :duracion, :genero_id)');
    $st->execute($fila);
}

/**
 * Modificar una pelicula
 * @param   PDO $pdo conexion con la base de datos
 * @param   array  $fila fila que se desea modificar en peliculas
 * @param   int $id de la pelicula que no se incluye en $fila
 */
function modificarPelicula($pdo, $fila, $id)
{
    $st = $pdo->prepare('UPDATE peliculas
                            SET titulo = :titulo
                                , anyo = :anyo
                                , sinopsis = :sinopsis
                                , duracion = :duracion
                                , genero_id = :genero_id
                            WHERE id = :id');
    $st->execute($fila + ['id' => $id]);
}

/**
 * Muestra las peliculas segun el PDOStatement dado por los buscadores
 * @param   PDOStatement $st resultado de la consulta, la cual se mostrará
 */
function mostrarPeliculas($st)
{
  ?>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-bordered table-hover table-striped">
          <thead>
              <th>Título</th>
              <th>Año</th>
              <th>Sinopsis</th>
              <th>Duración</th>
              <th>Género</th>
              <th>Acciones</th>
          </thead>
          <tbody>
              <?php
              if ($st !== false) {
                $var = 0; //Creo esta variable, para saber si entra en el while o no y mostrar un mensaje en caso de que no
                while ($fila = $st->fetch()): ?> <!-- Podemos asignarselo a fila, ya que en la asignación,
                                                        tb devuelve la fila, si la hay, por lo que entra,cuando no hay mas filas, da false y se sale.-->
                <tr>
                    <td><?= h($fila['titulo']) ?></td>
                    <td><?= h($fila['anyo']) ?></td>
                    <td><?= h($fila['sinopsis']) ?></td>
                    <td><?= h($fila['duracion']) ?></td>
                    <td><?= h($fila['genero']) ?></td>
                    <!--Al ser un enlace, la peticion es GET, por lo que le pasamos el id de la pelicula por la misma URL -->
                    <td><a href="confirm_borrado.php?id=<?= $fila['id'] ?>"
                           class="btn btn-xs btn-danger">
                           Borrar
                         </a>
                         <a href="modificar.php?id=<?= $fila['id'] ?>"
                           class="btn btn-xs btn-info">
                           Modificar
                         </a>
                    </td>
                </tr>
              <?php
                $var = 1;
                endwhile;
            }
            if (!$fila = $st->fetch()): ?>
                <?php if ($var === 0): ?>
                    <tr>
                        <td colspan="6">
                            <h3>No se han encontrado resultados con sus criterios.</h3>
                        </td>
                    </tr>
                <?php endif; ?>
          <?php endif;?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
}

/**
 * Formulario para modificar o insertar una pelicula
 * @param   array  $valores fila que se desea insertar o mod en peliculas
 * @param   array  $error array de errores
 * @param   PDO $pdo conexion con la base de datos
 * @param   string  $accion accion que se realiza, insertar o modificar
 */
function mostrarFormulario($valores, $error, $pdo, $accion)
{
    extract($valores);
    $st = $pdo->query('SELECT * FROM generos');
    $generos = $st->fetchAll();

    ?>
    <br>
    <div class="container">
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
                            <option value="<?= $genero['id'] ?>" <?= selected($genero['id'], $genero_id) ?>>
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
  </div>
    <?php
}

/**
 * Comprobamos si la pelicula existe y si no, exception
 * @param   PDO $pdo conexcion con la bd
 * @param   int $id ID de la pelicula a consultar
 * @return array|bool devuelve la pelicula o false
 */
function comprobarPelicula($pdo, $id)
{
    $fila = buscarPelicula($pdo, $id);
    if ($fila === false) {
        throw new ParamException();
    }
    return $fila;
}

/**
 * Compruebas si dos valores son iguales para marcar como selected
 * @param   int $genero id del genero de una bd
 * @param   int $genero_id id del genero de otra bd
 * @return string devuelve selected o '' si son iguales o no
 */
function selected($genero, $genero_id)
{
  return $genero == $genero_id ? "selected" : "";
}

/**
 * Muestra el cuerpo del modulo peliculas
 * @param   string $buscador valor por el que buscan ->titulo,año,...
 * @param   string $busca valor busca dado por la constante BUSCADORES para comparar
 * @param   array $error array para añadir errores
 * @param   array $st array de la sentencia de la pelicula
 */
function cuerpoPeliculas($error,$valores, $st,$generos)
{
    extract($valores);
  ?>
</div>
<div class="row form-inline" id="busqueda">
  <fieldset>
    <legend>Buscar</legend>
    <!-- Creamos un buscador de peliculas por titulo-->
    <form action="" method="get" class="form-inline">
      <div class="col-md-3">
        <div class="panel panel-default" id="fondoTabla">
          <div class="panel-body">
            <div class="form-group <?= hasError($buscarTitulo, $error) ?>">
              <label for="buscarTitulo">Buscar por título:</label>
              <input id="buscarTitulo" type="text" name="buscarTitulo"
              value="<?= $buscarTitulo ?>" class="form-control">
              <?php mensajeError($buscarTitulo, $error) ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default" id="fondoTabla">
          <div class="panel-body">
            <div class="form-group <?= hasError($buscarAnyo, $error) ?>">
              <label for="buscarAnyo">Buscar por año:</label>
              <input id="buscarAnyo" type="text" name="buscarAnyo"
              value="<?= $buscarAnyo ?>" class="form-control">
              <?php mensajeError($buscarAnyo, $error) ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default" id="fondoTabla">
          <div class="panel-body">
            <div class="form-group <?= hasError($buscarDuracion, $error) ?>">
              <label for="buscarDuracion">Buscar por duración:</label>
              <input id="buscarDuracion" type="text" name="buscarDuracion"
              value="<?= $buscarDuracion ?>" class="form-control">
              <?php mensajeError($buscarDuracion, $error) ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="panel panel-default" id="fondoTabla">
          <div class="panel-body">
            <div class="form-group <?= hasError($buscarGenero, $error) ?>">
                <label for="buscarAnyo">Buscar por género:</label>
                <select id="buscarGenero" name="buscarGenero" class="form-control">
                    <option value=""></option>
                    <?php foreach ($generos as $fila): ?>
                        <option value="<?= $fila['id'] ?>" <?= selected($fila['id'], $buscarGenero) ?> >
                            <?= $fila['genero'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
          </div>
        </div>
      </div>
      <input type="submit" value="Buscar" class="btn btn-primary">
    </form>
  </fieldset>
</div>
<hr>
<?= mostrarPeliculas($st); ?>
<div class="row">
  <div class="text-center">
    <a href="insertar.php" class="btn btn-info">Insertar una nueva película</a>
  </div>
</div>
</div>
<?php
}
