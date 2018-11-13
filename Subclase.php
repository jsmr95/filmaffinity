<?php

require 'Usuario.php';

class Subclase
{
    use Saludador;
    
    public static function quienSoy()
    {
        return 'Subclase';
    }

}
