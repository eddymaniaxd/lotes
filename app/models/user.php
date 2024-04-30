
<?php
require_once "../../config/conexion/db_mysql.php";

class User {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function index(){
        $sql = "SELECT * FROM users;";
        $users = $this->mysqli->query($sql);
        return $users;
    }

    function mostrar($id) {
        $sql = "SELECT * FROM users WHERE id = '$id'";
        $user = $this->mysqli->query($sql);
        return $user;
    }

    function agregar(string $nombre, string $telefono, string $correo, string $password, $rol){
        $sql = "INSERT INTO users(nombre, telefono, correo, password, fecha, rol)
                VALUES('$nombre', '$telefono', '$correo', '$password', now(), '$rol')";
        $result = $this->mysqli->query($sql);
        return $result;
    }

    function editar($id) {
        $sql = "SELECT * FROM users WHERE id = '$id'";
        $user = $this->mysqli->query($sql);
        return $user;
    }

    function actualizar($id, string $nombre, string $telefono, string $correo, string $password){
        $sql = "UPDATE users SET 
                        nombre = '$nombre',
                        telefono = '$telefono',
                        correo = '$correo',
                        password = '$password'
                        where id = '$id'
                ";
        $userUpdated = $this->mysqli->query($sql);
        return $userUpdated;
    }

    function eliminar($id){
        $sql = "DELETE FROM users WHERE id = '$id'";
        $userDeleted = $this->mysqli->query($sql);
        return $userDeleted;
    }
}