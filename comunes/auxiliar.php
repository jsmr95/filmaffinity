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

function navegador(){ ?>
  <nav class="navbar navbar-default">
<div class="container-fluid">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <a class="navbar-brand " href="./peliculas/index.php">Películas</a>
    <a class="navbar-brand " href="./generos/index.php">Géneros</a>
  </div>
</div><!-- /.container-fluid -->
</nav>
<?php
}
