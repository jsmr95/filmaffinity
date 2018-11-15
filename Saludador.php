<?php

namespace espacio5;

//RASGO
trait Saludador
{
    public $mensaje = "Hola\n";
    public function saluda()
    {
        echo $this->mensaje;
    }
}
