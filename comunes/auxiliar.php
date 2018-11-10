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
                    <a class="navbar-brand " href="">Menú</a>
                    <a class="navbar-brand " href="./peliculas/index.php">Películas</a>
                    <a class="navbar-brand " href="./generos/index.php">Géneros</a>
                </div>
                <div class="navbar-text navbar-right">
                    <a href="login.php" class="btn btn-success">Login</a>
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
                      <a href="../login.php" class="btn btn-success">Login</a>
                  </div>
              </div>
          </nav>
  <?php
}

function comprobarLogin(&$error){
    $login = trim(filter_input(INPUT_POST, 'login'));
    if ($login === '') {
        $error['login'] = 'El nombre de usuario no puede estar vacío.';
    }
}

function comprobarPassword(&$error){
    $pass = trim(filter_input(INPUT_POST, 'password'));
    if ($pass === '') {
        $error['password'] = 'La contraseña no puede estar vacía.';
    }
}

function comprobarUsuario($valores, $pdo, &$error){
    extract($valores);
    $st = $pdo->prepare('SELECT * FROM usuarios WHERE login = :login');
    $st->execute(['login' => $login]);
    $fila = $st->fetch();
    if ($fila !== false) {
        if (password_verify($password, $fila['password'])) {
            return;
        }
    }
    $error['sesion'] = 'El usuario o la contraseña son incorrectos.';
}
