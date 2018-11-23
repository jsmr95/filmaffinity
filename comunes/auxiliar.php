<?php

/**
 * CONSTANTES PARA EL PROGRAMA
 * @param   const $PAR_LOGIN constante que incluye los valores al logear
 * @param   const $PAR_CREAR_CUENTA constante que incluye los valores al crear cuenta
 */
const PAR_LOGIN = ['login' => '', 'password' => ''];
const PAR_CREAR_CUENTA = ['login' => '', 'password' => '', 'passwordRepeat' => ''];

/**
 * EXCEPCIONES PARA EL PROGRAMA
 * @param   class ValidationException exception que se lanza en un determinado error.
 * @param   class ParamException exception que se lanza en un determinado error.
 * @param   class EmptyParamException exception que se lanza en un determinado error.
 */
class ValidationException extends Exception
{}

class ParamException extends Exception
{}

class EmptyParamException extends Exception
{}

  /**
   * Realiza una conexion con la base de datos
   * @return PDO  Devuelve la sentencia PDO de la conexion a la base de datos
   */
function conectar()
{
    return new PDO('pgsql:host=localhost;dbname=fa','fa','fa');
}

/**
 * Comprueba las variables $_SESSION para saber si hay que mostrar algun mensaje
 * @param   string $var nombre de la clave del array $_SESSION
 * @param   string $tipo  Indica el tipo de alert que mostrará, si danger, info,...
 */
function compruebaSession($var, $tipo)
{ ?>
  <br>
    <?php if (isset($_SESSION["$var"])): ?>
        <div class="row">
            <div class="alert alert-<?=$tipo?>" role="alert">
                <?= $_SESSION["$var"] ?>
            </div>
        </div>
        <?php unset($_SESSION["$var"]); ?>
    <?php endif;
}

/**
 * Comprueba si algun parametro existe y si no lo inicializa a ''
 * @param   string $buscador valor de la variable que se deberia pasar por $_GET
 * y comprueba si existe y no es null.
 * @return string devuelve el valor de la variable si existe y si no, devuelve ''.
 */
function existe($buscador)
{
  return isset($_GET[$buscador]) ? trim($_GET[$buscador]) : '';
}

/**
 * Comprueba si existe un usuario determinado
 * @param   PDO $pdo Conexion con la base de datos
 * @param   int $id     id del usuario que queremos buscar
 * @return array  devuelve la fila de la consulta si existiese y si no, FALSE
 */
function buscarUsuario($pdo, $id)
{
    $st = $pdo->prepare('SELECT * from usuarios WHERE id = :id');
    //si no hay alguna fila que cumple con el id, te manda a la misma pagina
    $st->execute([':id' => $id]);
    //Te devuelve la pelicula, si no esta, devuelve FALSE
    return $st->fetch();
}

/**
 *Función para insertar un usuario
 * @param   PDO $pdo     Objeto PDO usado para conectar con la bd
 * @param   array $fila  fila del alumno a insertar
 */
function insertarUsuario($pdo, $fila)
{
  $st = $pdo->prepare('INSERT INTO usuarios (login, password)
  VALUES (:login,:password)');
  $st->execute([':login'=>$fila['login'],':password'=>password_hash($fila['password'],PASSWORD_DEFAULT)]);
}

/**
 * Comprobamos si el ID es válido.
 * @return int  $id Devuelve el $id en caso de que valide, si no salta Excepcion
 */
function comprobarId()
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === null || $id === false) {
        throw new ParamException();
    }
    return $id;
}

/**
 * Comprobamos el array de errores, si no esta vacío lanza Exception
 * @param   array $error array de errores
 */
function comprobarErrores(&$error)
{
    if (!empty($error)) {
        throw new ValidationException();
    }
}

/**
 * Comprueba si los parametros pasados por POST son los que se esperan, lanza
 *excepciones si no es asi.
 * @param   string $par parametros a comprobar
 */
function comprobarParametros($par)
{
    if (empty($_POST)) {
        throw new EmptyParamException();
    }
    if(!empty(array_diff_key($par, $_POST)) ||
        !empty(array_diff_key($_POST,$par))) {
            throw new ParamException();
        }
}

/**
 * Comprobar si al logear se ha introducido un usuario
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @return string $login si es '' -> error, si no, devuelve el login
 */
function comprobarLogin(&$error)
{
  $login = trim(filter_input(INPUT_POST, 'login'));
  if ($login === '') {
      $error['login'] = 'El nombre de usuario no puede estar vacío.';
  }else {
      return $login;
  }
}

/**
 * Comprobar si al logear se ha introducido una pass
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @return string $pass si es '' -> error, si no, devuelve la pass
 */
function comprobarPassword(&$error)
{
  $pass = trim(filter_input(INPUT_POST, 'password'));
  if ($pass === '') {
      $error['password'] = 'La contraseña no puede estar vacía.';
  }else {
      return $pass;
  }
}

/**
 * Comprueba si las contraseñas coinciden y la repeticion no esta vacia en
 * la creacion de una cuenta
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @param   string $password variable de la pass que compararemos con la repeticion
 * @return string|bool $pass si no es '' y coinciden y false si no coincide o la
 * repeticion es ''
 */
function comprobarPasswordNueva($password, &$error)
{
  $pass = trim(filter_input(INPUT_POST, 'passwordRepeat'));
  if ($pass === '') {
    $error['passwordRepeat'] = 'Debes repetir la contraseña, no puede estar vacío.';
    return false;
  }elseif ($pass !== $password) {
    return false;
  }else {
    return $pass;
  }
}

/**
 * Comprueba si existe el usuario indicado en el array
 * $valores, con el nombre y la contrasxeña dados.
 * @param   array $valores nombre y la contraseña
 * @param   PDO $pdo     Objeto PDO usado para buscar al usuario
 * @return array|bool          La fila del usuario si existe; o false si no.
 */

function comprobarUsuario($valores, $pdo)
{
  extract($valores);
   $st = $pdo->prepare('SELECT *
                          FROM usuarios
                         WHERE login = :login');
   $st->execute(['login' => $login]);
  $fila = $st->fetch();
  if ($fila !== false) {
      if (password_verify($password, $fila['password'])) {
          return $fila;
      }
  }
  return false;
}

/**
 * Cmprobar si el usuario que queremos crear, no existe ya
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @param   array $valores valores del usuario a insertar
 * @param   PDO $pdo conexion con la base de datos usuarios
 * @return array|bool $fila si el usuario ya existe o false si no existe
 */
function comprobarUsuarioNuevo($valores, $pdo, &$error)
{
  extract($valores);
  $st = $pdo->prepare('SELECT *
                        FROM usuarios
                       WHERE login = lower(:login)');
  $st->execute(['login' => $login]);
  $fila = $st->fetch();
  if ($fila !== false) {
    return $fila;
  } else {
    return false;
  }
}

/**
 * Compueba si el usuario esta logueado para permitirle borrar
 * @param   string $modulo peliculas o generos
 */
function compruebaLogueadoBorrar($modulo)
{
  //El usuario debe estar logeado para poder borrar peliculas
  if (!isset($_SESSION['usuario'])) {
     $_SESSION['error'] = "Debe iniciar sesión para poder borrar $modulo";
     irAlIndice();
 } elseif ($_SESSION['usuario'] != 'admin') {
     $_SESSION['error'] = "Debe ser administrador para poder borrar $modulo";
     irAlIndice();
 }
}

/**
 * Compueba si el usuario esta logueado para permitirle modificar
 * @param   string $modulo peliculas o generos
 */
function compruebaLogueadoModificar($modulo)
{
  //Debe estar logueado para modificar una pelicula
  if (!isset($_SESSION['usuario'])) {
        $_SESSION['error'] = "Debe iniciar sesión para modificar $modulo.";
        irAlIndice();
    }
}

/**
 * Comprueba en el array si existe una clave
 * @param   string $key Clave a buscar en el array error
 * @param   array $error array para buscar si tiene una clave
 * @return string devuelve 'has-error' si true, o '' si false
 */
function hasError($key, $error)
{
    return array_key_exists($key, $error) ? 'has-error' : '';
}

/**
 * Pinta un error si existe cierta clave en el array error
 * @param   array $error variable error para saber si contiene la clave
 * @param   string $key clave del error
 */
function mensajeError($key, $error)
{
    if (isset($error[$key])){ ?>
        <small class="help-block"> <?= $error[$key] ?></small> <?php }
}

/**
 * Muestra el pequeño formulario para crear_cuenta
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @param   array $valores contiene los valores que se pasan por post
 */
function mostrarCrearCuenta($valores, &$error)
{
  ?>
  <div class="row">
    <div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Crear una nueva cuenta</h3>
        </div>
        <div class="panel-body">
          <form action="" method="post">
            <div class="form-group <?= hasError('login', $error) ?>">
              <label for="login" class="control-label">Usuario</label>
              <input id="login" type="text" name="login"
              class="form-control" value="<?= h($valores['login']) ?>">
              <?php mensajeError('login', $error) ?>
            </div>
            <div class="form-group <?= hasError('password', $error) ?>">
              <label for="password" class="control-label">Contraseña</label>
              <input id="password" type="password" name="password"
              class="form-control" value="<?= h($valores['password']) ?>">
              <?php mensajeError('password', $error) ?>
            </div>
            <div class="form-group <?= hasError('passwordRepeat', $error) ?>">
              <label for="passwordRepeat" class="control-label">Vuelva a introducir la contraseña:</label>
              <input id="passwordRepeat" type="password" name="passwordRepeat"
              class="form-control" value="<?= h($valores['passwordRepeat']) ?>">
              <?php mensajeError('passwordRepeat', $error) ?>
            </div>
            <input type="submit" value="Crear cuenta"
            class="btn btn-success">
            <a href="login.php" class="btn btn-info">Volver</a>
          </form>
      </div>
    </div>
  </div>
  </div>
</div>
<?php
}

/**
 * Muestra el MENÚ inicial
 */
function mostrarMenu()
{
  ?>
  <div class="container">
    <br>
    <?= compruebaSession('login','info'); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="center-block">
          <div class="panel panel-success">
            <div class="panel-heading text-center"> FILMAFFINITY </div>
            <div class="panel-body">
              <pre> <h3><p align="center">Bienvenidos a la página Oficial de Film-Affinity.</p></h3>
                <h5><p align="center"> Podrás navegar por las diferentes opciones del navegador! Espero que encuentres la película que buscas!
                </p></h5>
              </pre>
            <div class="row">
              <div class="col-md-6">
                <div class="panel-body ">
                  <pre>
                     <a style="padding:2em;display: flex;margin: auto;" href="./peliculas/index.php"
                     class="btn btn-info"> Visite nuestro módulo de Peliculas. </a>
                  </pre>
              </div>
            </div>
              <div class="col-md-6">
                <div class="panel-body">
                  <pre>
                    <a style="padding:2em;display: flex;margin: auto" href='./generos/index.php'
                    class="btn btn-info"> Visite nuestro módulo de Géneros. </a>
                  </pre>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Muestra el pequeño formulario para iniciar sesion
 * @param   array $error variable error para añadir error en caso de necesitarlo
 * @param   array $valores contiene los valores que se pasan para logear
 */
function mostrarLogin($valores, &$error)
{
  ?><div class="row">
    <div class="col-md-4">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Iniar sesión</h3>
        </div>
        <div class="panel-body">
          <form class="" action="" method="post">
              <div class="form-group <?= hasError('login', $error) ?>">
                  <label for="login" class="control-label">Usuario</label>
                  <input id="login" type="text" name="login"
                         class="form-control" value="<?=$valores['login']?>">
                  <?php mensajeError('login', $error) ?>
              </div>
              <div class="form-group <?= hasError('password', $error) ?>">
                  <label for="password" class="control-label">Contraseña:</label>
                  <input id="password" type="password" name="password"
                         class="form-control" value="<?=$valores['password']?>">
                  <?php mensajeError('password', $error) ?>
              </div>
           <button type="submit" class="btn btn-default">Iniciar sesión </button>
           <a href="crear_cuenta.php" class="btn btn-default">Crear cuenta </a>
         </form>
       </div>
       </div>
    </div>
  </div>
 </div>
 <?php
}

/**
 * Devuelve todos los generos
 * @param   PDO $pdo conexion con la base de datos
 * @return  PDOStatement sentencia de la consulta realizada
 */
function recogerGeneros($pdo)
{
    return $pdo->query('SELECT * FROM generos')->fetchAll();
}

/**
 * Sanea una cadena
 * @param   string $cadena cadena a sanear
 * @return  string cadena ya saneada.
 */
function h($cadena)
{
    return htmlspecialchars($cadena, ENT_QUOTES);
}

/**
 * Vuelve al indice del módulo que estes
 */
function irAlIndice()
{
  header("Location: index.php");
}

/**
 * Muestra un navegador en el menú principal
 */
function navegadorInicio()
{ ?>
<nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">FilmAffinity</a>
      </div>
      <div class="navbar-text navbar-right">
        <?php if (isset($_SESSION['usuario'])):?>
          <a class="label label-info glyphicon glyphicon-user" href="./comunes/modificar_usuario.php">
             <?= $_SESSION['usuario']?>
          </a>
          <?php $_SESSION['url'] = $_SERVER["REQUEST_URI"]; ?>
        <a href="../logout.php" class="btn btn-success">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
        </a>
        <?php else: ?>
        <a href="../login.php" class="btn btn-success">
          <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Login
        </a>
    <?php endif; ?>
      </div>
    </div>
  </nav>
<?php
}

/**
 * Muestra un navegador en el resto de módulos que no sea menu principal
 */
function navegador()
{ ?>
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
          <a class="navbar-brand " href="../index.php">Menú</a>
          <a class="navbar-brand " href="../peliculas/index.php">Películas</a>
          <a class="navbar-brand " href="../generos/index.php">Géneros</a>
      </div>
      <div class="navbar-text navbar-right">
        <?php if (isset($_SESSION['usuario'])):?>
          <a class="label label-info glyphicon glyphicon-user"> <?= $_SESSION['usuario']?></a>
          <?php $_SESSION['url'] = $_SERVER["REQUEST_URI"]; ?>
        <a href="../logout.php" class="btn btn-success">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span> Logout
        </a>
        <?php else: ?>
        <a href="../login.php" class="btn btn-success">
          <span class="glyphicon glyphicon-user" aria-hidden="true"></span> Login
        </a>
      <?php endif; ?>
      </div>
    </div>
  </nav>
  <?php
}

/**
 * Genera un div en el pie, para recordar al usuario que usamos cookies
 * @param   string $pagina pagina en la que está para cuando cree la cookie
 * , el crear_cookie lo devuelva a la  pagina donde estaba
 */
function politicaCookies($pagina)
{
  if (!isset($_COOKIE['acepta'])): ?>
  <nav class="navbar navbar-default navbar-fixed-bottom navbar-inverse">
    <div class="container">
        <p class="navbar-text">Tienes que aceptar las politicas de cookies.</p>
        <p class="navbar-text navbar-right">
          <?php $_SESSION['pagina'] = $pagina; ?>
            <a href="\comunes\crear_cookie.php" class="btn btn-success">Aceptar Cookies</a>
        </p>
    </div>
  </nav>
  <?php endif;
}

/**
 * Pregunto si esta seguro de borrar la fila
 * @param   int $id id de la pelicula o genero a borrar
 */
function preguntaSiEstaSeguroBorrar($id)
{
  ?>
  <div class="container">
    <div class="row">
      <h3>¿Seguro que deseas borrar la fila ?</h3>
      <div class="col-mg-4">
        <form action="index.php" method="post" class="form-inline">
          <input type="hidden" name="id" value="<?= $id ?>">
          <input type="submit" value="Si" class="form-control btn btn-danger">
          <a href="index.php" class="btn btn-success">No</a>
        </form>
      </div>
    </div>
  </div>
  <?php
}

/**
 * Muestro un pie de pagina en todos los modulos
 */
function piePagina()
{?>
  <nav class="navbar navbar-default navbar-fixed-bottom">
    <div class="container">
      <div class="navbar-header">
        <br>
        <span class="glyphicon glyphicon-copyright-mark" aria-hidden="true">Copyright 2018 - Jose María Gallego Martel</span>
      </div>
      <div class="navbar-text navbar-right">
        <h4>FilmAffinity <span class="glyphicon glyphicon-registration-mark" aria-hidden="true"></span> </h4>
      </div>
    </div>
  </nav>
  <?php

}
