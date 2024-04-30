
<?php
require_once "../../config/conexion/db_mysql.php";

class Lote {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function index(){
        // $sql = "SELECT * FROM lotes WHERE UV = '12A' AND Manzado = 12;";
        $sql = "SELECT * FROM lotes";
        $lotes = $this->mysqli->query($sql);
        return $lotes;
    }

    function buscarPorUV($uv){
        $sql = "SELECT * FROM lotes WHERE UV = '$uv'";
        $lotes = $this->mysqli->query($sql);
        return $lotes;
    }

    function buscarPorItemCode($itemCode) {
        $sql = "SELECT * FROM lotes WHERE ItemCode = '$itemCode'";
        $user = $this->mysqli->query($sql);
        return $user;
    }

    function mostrar($id) {
        $sql = "SELECT * FROM lotes WHERE id = '$id'";
        $user = $this->mysqli->query($sql);
        return $user;
    }

    function actualizarEstado($id, $estado) {
        $sql = "UPDATE lotes SET Estado = '$estado' WHERE id = '$id'";
        $this->mysqli->query($sql);
    }

    function actualizarEstadoWithItemCode($itemCode, $estado) {
        $sql = "UPDATE lotes SET Estado = '$estado' WHERE ItemCode = '$itemCode'";
        return $this->mysqli->queryWithReturnID($sql);
    }

    function buscarLoteLibre($idLote){
        $sql = "SELECT * FROM lotes
                WHERE id = '$idLote' AND Estado = 'LIBRE'
                ";
        return $this->mysqli->query($sql);
    }

    function buscarLoteUVMznLote($uv, $mzn, $lote){
        $sql = "SELECT * FROM lotes
                WHERE UV='$uv' AND Manzado='$mzn' AND Lote='$lote'
                ";
        return $this->mysqli->query($sql);
    }

    function actualizarLoteWithItemCode($itemCode, $proyectos, $uv, $manzano, $lote, $estado, $superficie, $modelo, $precio, $cuota){
        $sql = "UPDATE lotes SET Proyectos='$proyectos', UV='$uv', Manzado='$manzano', Lote='$lote', Estado = '$estado', Superficie='$superficie', Modelo='$modelo', Precio='$precio', Cuota='$cuota', updated_at=now() WHERE ItemCode = '$itemCode'";
        return $this->mysqli->queryWithReturnID($sql);
    }

}