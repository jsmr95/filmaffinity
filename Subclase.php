<?php

require 'Usuario.php';

class Subclase extends Usuario
{
    public $nombre;
    public function __construct($id,$nombre)
    {
        parent::__construct($id);
        $this->nombre = $nombre;
    }

    public static function quienSoy()
    {
        return 'Subclase de ' . parent::quienSoy();
    }

}
