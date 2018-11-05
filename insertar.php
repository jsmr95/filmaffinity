<!DOCTYPE html>
<html lang="es" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Insertar una nueva película</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
      <?php
      require './auxiliar.php';

        const PAR = [
            'titulo' => '',
            'anyo' => '',
            'sinopsis' => '',
            'duracion' => '',
            'genero_id' => '',
        ];
         extract(PAR);




         if (isset($_POST['titulo'], $_POST['anyo'], $_POST['sinopsis'],
                  $_POST['duracion'], $_POST['genero_id'])) {
            extract(array_map('trim', $_POST), EXTR_IF_EXISTS);

            $error = [];
            $fltAnyo = filter_input(INPUT_POST,'anyo',FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9999], ]);
            if ($fltAnyo === false) {
                $error[] = 'El año no es correcto.';
            }

            $fltTitulo = trim(filter_input(INPUT_POST, 'titulo'));
            if (mb_strlen($fltTitulo) > 255) {
                $error[] = 'El titulo es demasiado largo.';
            }

            $fltSinopsis = trim(filter_input(INPUT_POST,'sinopsis'));
            $fltDuracion = trim(filter_input(INPUT_POST, 'duracion'));
            if (mb_strlen($fltDuracion) !== '') {
                $fltDuracion = filter_input(INPUT_POST, 'duracion', FILTER_VALIDATE_INT, ['options' => [
                    'min_range' => 0,
                    'max_range' => 32767,
                ],
            ]);
            if ($fltDuracion === false) {
                $error[] = 'La duración no es correcta.';
            }
        } else {
            $fltDuracion = null;
        }

            if (empty($error)) {
                $pdo = conectar();
                $st = $pdo->prepare('INSERT INTO peliculas (titulo, anyo, sinopsis, duracion, genero_id)
                VALUES (:titulo, :anyo, :sinopsis, :duracion, :genero_id)');
                $st->execute([
                    ':titulo' => $fltTitulo,
                    ':anyo' => $fltAnyo,
                    ':sinopsis' => $fltSinopsis,
                    ':duracion' => $fltDuracion,
                    ':genero_id' => $fltGenero_id,
                ]);
                header('Location: index.php');
            } else {
                foreach ($error as $err) {
                    echo "<h4>Error: $err</h4>";
                }
            }
         }

        ?>
        <br>
        <div class="container">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Insertar una nueva película...</h3>
                </div>
                <div class="panel-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input type="text" name="titulo" class="form-control" id="titulo" value="<?= $titulo ?>">
                        </div>
                        <div class="form-group">
                            <label for="anyo">Año</label>
                            <input type="text" name="anyo" class="form-control" id="anyo" value="<?= $anyo ?>">
                        </div>
                        <div class="form-group">
                            <label for="sinopsis">Sinopsis</label>
                            <textarea name="sinopsis" rows="8" cols="80" class="form-control" id="sinopsis"><?= $sinopsis ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="duracion">Duración</label>
                            <input type="text" name="duracion" class="form-control" id="duracion" value="<?= $duracion ?>">
                        </div>
                        <div class="form-group">
                            <label for="genero_id">Género</label>
                            <input type="text" name="genero_id" class="form-control" id="genero_id" value="<?= $genero_id ?>">
                        </div>
                        <input type="submit" value="Insertar" class="btn btn-success">
                        <a href="index.php" class="btn btn-info">Volver</a>
                    </form>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </body>
</html>
