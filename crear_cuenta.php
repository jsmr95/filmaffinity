<?php session_start();
require 'comunes/auxiliar.php';
navegador();
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Crear una nueva cuenta</h3>
                </div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group <?= hasError('login', $error) ?>">
                            <label for="login" class="control-label">Usuario</label>
                            <input id="login" type="text" name="login"
                            class="form-control" value="<?= h($titulo) ?>">
                            <?php mensajeError('login', $error) ?>
                        </div>
                        <div class="form-group <?= hasError('password', $error) ?>">
                            <label for="password" class="control-label">Contraseña</label>
                            <input id="password" type="text" name="password"
                            class="form-control" value="<?= h($password) ?>">
                            <?php mensajeError('password', $password) ?>
                        </div>
                        <div class="form-group <?= hasError('password1', $error) ?>">
                            <label for="password1" class="control-label">Vuelva a introducir la contraseña:</label>
                            <input id="password1" type="text" name="password1"
                            class="form-control" value="<?= h($password1) ?>">
                            <?php mensajeError('password1', $password1) ?>
                        </div>
                            <input type="submit" value="Crear cuenta"
                            class="btn btn-success">
                            <a href="index.php" class="btn btn-info">Volver</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php piePagina(); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
    </html>
