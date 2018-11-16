<?php

namespace espacio1;

require './Saludador.php';

function strlen($c)
{
    return \strlen($c) - 1;
}

class Usuario
{

    const ADMIN = 'admin';

    public $id;
    public $login;
    public $password;
    public static $cantidad = 0;

    public static function longitud($c)
    {
        return strlen($c);
    }

    public function __construct($id)
    {
        require_once './comunes/auxiliar.php';
        $pdo = conectar();
        $usuario = buscarUsuario($pdo, $id);
        $this->id = $usuario['id'];
        $this->login = $usuario['login'];
        $this->password = $usuario['password'];
        self::$cantidad++;
    }

    public function __destruct()
    {
        self::$cantidad--;
    }

    public function desloguear()
    {
        $nombre = $this->login; //this hace referencia a la instancia del objeto actual
        echo "Ya está deslogueado $nombre.";
    }

    public static function nombreAdmin() //Lo hacemos static para poder llamarlo sin tener que instanciarlo, se puede llamar desde la misma clase.
    {
        return self::ADMIN; //self es la clase actual, para evitar el nombre de la clase
    }

    public static function quienSoy()
    {
        return __CLASS__;
    }

    public static function prueba()
    {
        return static::quienSoy();
    }

    public function sumaId($x)
    {
        return $x + $this->id;
    }
}

function multiplicar($v)
{
    return function($x) use ($v){ //Clausura, recuerda el valor de la variable $v aunque se haya salido del ambito, vale lo que valía en el momento de la ejecución de la funcion
        return $x * $v;
    };
}

/* TEORIA DE CALLABLES, PASAR AL ARCHIVO DE TEORIA
    Formas de Callables ->
        -'funcion'
        - function($x){return $x;}
        -['nombre clase con su espacio','nombre de metodo estatico']
        -[$ObjetoOInstancia, 'nombre del metodo publico']
        
        */
