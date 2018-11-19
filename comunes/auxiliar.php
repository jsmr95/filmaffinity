<?php

const PAR_LOGIN = ['login' => '', 'password' => ''];
const PAR_CREAR_CUENTA = ['login' => '', 'password' => '', 'passwordRepeat' => ''];

class ValidationException extends Exception
{}

class ParamException extends Exception
{}

class EmptyParamException extends Exception
{}

function conectar()
{
    return new PDO('pgsql:host=localhost;dbname=fa','fa','fa');
}

function compruebaSession($var, $tipo){ ?>
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

function buscarUsuario($pdo, $id)
{
    $st = $pdo->prepare('SELECT * from usuarios WHERE id = :id');
    //si no hay alguna fila que cumple con el id, te manda a la misma pagina
    $st->execute([':id' => $id]);
    //Te devuelve la pelicula, si no esta, devuelve FALSE
    return $st->fetch();
}

function insertarUsuario($pdo, $fila){
  $st = $pdo->prepare('INSERT INTO usuarios (login, password)
  VALUES (:login,:password)');
  $st->execute([':login'=>$fila['login'],':password'=>password_hash($fila['password'],PASSWORD_DEFAULT)]);
}

function comprobarId(){
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === null || $id === false) {
        throw new ParamException();
    }
    return $id;
}

function comprobarErrores(&$error){
    if (!empty($error)) {
        throw new ValidationException();
    }
}

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

function comprobarLogin(&$error){
  $login = trim(filter_input(INPUT_POST, 'login'));
  if ($login === '') {
      $error['login'] = 'El nombre de usuario no puede estar vacío.';
  }else {
      return $login;
  }
}

function comprobarPassword(&$error){
  $pass = trim(filter_input(INPUT_POST, 'password'));
  if ($pass === '') {
      $error['password'] = 'La contraseña no puede estar vacía.';
  }else {
      return $pass;
  }
}

function comprobarPasswordNueva($password, &$error){
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
 * @param  array $error   array de errores si los hay
 * @return array|bool          La fila del usuario si existe; o false si no.
 */

function comprobarUsuario($valores, $pdo){
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

function comprobarUsuarioNuevo($valores, $pdo, &$error){
  extract($valores);
  $st = $pdo->prepare('SELECT *
                        FROM usuarios
                       WHERE login = :login');
  $st->execute(['login' => $login]);
  $fila = $st->fetch();
  if ($fila !== false) {
    return $fila;
  } else {
    return false;
  }
}


function hasError($key, $error){

    return array_key_exists($key, $error) ? 'has-error' : '';
}

function mensajeError($key, $error){
    if (isset($error[$key])){ ?>
        <small class="help-block"> <?= $error[$key] ?></small> <?php }
}

function h($cadena){
    return htmlspecialchars($cadena, ENT_QUOTES);
}

function irAlIndice(){
  header("Location: index.php");
}

function navegadorInicio(){ ?>
<nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand">FilmAffinity</a>
      </div>
      <div class="navbar-text navbar-right">
        <?php if (isset($_SESSION['usuario'])):?>
          <span class="label label-info glyphicon glyphicon-user"> <?= $_SESSION['usuario']?></span>
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

function navegador(){ ?>
  <nav class="navbar navbar-default">
    <div class="container">
      <div class="navbar-header">
          <a class="navbar-brand " href="../index.php">Menú</a>
          <a class="navbar-brand " href="../peliculas/index.php">Películas</a>
          <a class="navbar-brand " href="../generos/index.php">Géneros</a>
      </div>
      <div class="navbar-text navbar-right">
        <?php if (isset($_SESSION['usuario'])):?>
          <span class="label label-info glyphicon glyphicon-user"> <?= $_SESSION['usuario']?></span>
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

function politicaCookies($pagina){
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

function piePagina(){?>
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
