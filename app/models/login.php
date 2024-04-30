<?php
require_once "../../config/conexion/db_mysql.php";
// namespace conexiondb\app\models;

class Login {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function ingresar($email, $password)
    {
        $sql = "SELECT * FROM users WHERE correo = '$email' AND password = '$password'";
        $result = $this->mysqli->query($sql);
        return $result;
    }
}