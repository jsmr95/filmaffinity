<?php

class Usuario
{
    const ADMIN = 'admin';

    public $id;
    public $login;
    public $password;
    public static $cantidad = 0;

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
}
