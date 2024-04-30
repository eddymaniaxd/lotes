
<?php
require_once "../../config/conexion/db_mysql.php";

class Cliente {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function index(){
        $sql = "SELECT * FROM clientes";
        $clientes = $this->mysqli->query($sql);
        return $clientes;
    }

    function mostrar($id) {
        $sql = "SELECT * FROM clientes WHERE id = '$id'";
        $cliente = $this->mysqli->query($sql);
        return $cliente;
    }

    function agregar($ci, $nombre, $telefono, $email){
        $sql = "INSERT INTO clientes(ci, nombre, telefono, email)
                VALUES('$ci', '$nombre', '$telefono', '$email')";
        $result = $this->mysqli->query($sql);
        return $result;
    }

    function agregarAndReturnID($ci, $nombre, $telefono, $email){
        if($ci){
            $sql = "INSERT INTO clientes(ci, nombre, telefono, email)
                    VALUES('$ci', '$nombre', '$telefono', '$email')";
        }else{
            $sql = "INSERT INTO clientes(nombre, telefono, email)
                    VALUES('$nombre', '$telefono', '$email')";
        }
        $result = $this->mysqli->queryWithReturnID($sql);
        return $result;
    }

    function buscarPorCI($ci){
        $sql = "SELECT * FROM clientes WHERE ci = '$ci'";
        $cliente = $this->mysqli->query($sql);
        return $cliente;
    }
}