<?php

require_once "../models/login.php";
// namespace conexiondb\app\business;

session_start();
class LoginService {

    private $login;

    function __construct()
    {
        $this->login = new Login();
    }

    function ingresar(string $correo, string $password)
    {
        $result = $this->login->ingresar($correo, $password);
        return $result;
    }

    function veriricarAutenticacion(){
        $validar = $_SESSION["user"];
        if( $validar == null || $validar = ''){
            return false;
        }
        return true;
    }

    function logout(){
        session_destroy();
        // header("Location: ../../views/login");
    }
}

// $correo = $_POST["correo"];
// $password = $_POST["password"];
// session_start();
// $_SESSION["correo"] = $correo;
// $loginBusiness->ingresar($correo, $password);
