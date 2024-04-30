
<?php
require_once "../../config/conexion/db_mysql.php";

class HistorialMigracion {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function index(){
        $sql = "SELECT * FROM historial_migracion";
        $lotes = $this->mysqli->query($sql);
        return $lotes;
    }

    function mostrar($id) {
        $sql = "SELECT * FROM historial_migracion WHERE id = '$id'";
        $user = $this->mysqli->query($sql);
        return $user;
    }

    function agregar($item_code_lote, $estado_anterior, $estado_entrante) {
        $sql = "INSERT INTO historial_migracion(item_code_lote, estado_anterior, estado_entrante, fecha_creacion)
                VALUES('$item_code_lote', '$estado_anterior', '$estado_entrante', now())";
        $result = $this->mysqli->queryWithReturnID($sql);
        return $result;
    }

}