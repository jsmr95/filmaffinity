<?php

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

function comprobarId(){
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === null || $id === false) {
        throw new ParamException();
    }
    return $id;
}

function navegadorInicio(){ ?>
<nav class="navbar navbar-default">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand " href="index.php">Menú</a>
                    <a class="navbar-brand " href="./peliculas/index.php">Películas</a>
                    <a class="navbar-brand " href="./generos/index.php">Géneros</a>
                </div>
                <div class="navbar-text navbar-right">
                    <?php if (isset($_SESSION['usuario'])):?>
                        <?= $_SESSION['usuario']; ?>
                    <a href="logout.php" class="btn btn-success">Logout</a>
                    <?php else: ?>
                    <a href="login.php" class="btn btn-success">Login</a>
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
                          <?= $_SESSION['usuario']; ?>
                      <a href="../logout.php" class="btn btn-success">Logout</a>
                      <?php else: ?>
                      <a href="../login.php" class="btn btn-success">Login</a>
                  <?php endif; ?>
                  </div>
              </div>
          </nav>
  <?php
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

/**
 * Comprueba si existe el usuario indicado en el array
 * $valores, con el nombre y la contrasxeña dados.
 * @param   array $valores nombre y la contraseña
 * @param   PDO $pdo     Objeto PDO usado para buscar al usuario
 * @param  array $error   array de errores si los hay
 * @return array|bool          La fila del usuario si existe; o false si no.
 */

function comprobarUsuario($valores, $pdo, &$error){
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
    $_SESSION['sesion'] = 'El usuario o la contraseña son incorrectos.';
    return false;
}

function buscarUsuario($pdo, $id)
{
    $st = $pdo->prepare('SELECT * from usuarios WHERE id = :id');
    //si no hay alguna fila que cumple con el id, te manda a la misma pagina
    $st->execute([':id' => $id]);
    //Te devuelve la pelicula, si no esta, devuelve FALSE
    return $st->fetch();
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
