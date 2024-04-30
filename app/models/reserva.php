
<?php
require_once "../../config/conexion/db_mysql.php";

class Reserva {

    private $mysqli;

    function __construct()
    {
        $this->mysqli = new DbMySQL();
    }

    function index(){
        $sql = " SELECT  lo.*,
                    re.id as 'reserva_id',
                    re.comentario,
                    re.estado,
                    re.fecha_duracion,
                    re.fecha_creacion,
                    re.fecha_baja,
                    re.fecha_deposito,
                    re.fecha_liberacion,
                    us.id as 'user_id',
                    us.nombre as 'user_nombre', 
                    us.correo as 'user_correo', 
                    us.telefono as 'user_telefono', 
                    us.rol as 'user_rol', 
                    cli.ci as 'cliente_ci',
                    cli.nombre as 'cliente_nombre',
                    cli.telefono as 'cliente_telefono',
                    cli.email as 'cliente_email'
                    FROM reserva re, lotes lo, users us, clientes cli
                    WHERE
                    re.lote_id = lo.id AND
                    re.user_id = us.id AND
                    re.cliente_id = cli.id AND
                    re.estado = 1;
                ";
        $reservas = $this->mysqli->query($sql);
        return $reservas;
    }

    function mostrarTodasReservas(){
        $sql = " SELECT  lo.*,
                    re.id as 'reserva_id',
                    re.comentario,
                    re.estado,
                    re.fecha_duracion,
                    re.fecha_creacion,
                    re.fecha_baja,
                    re.fecha_deposito,
                    re.fecha_liberacion,
                    us.nombre as 'user_nombre', 
                    us.correo as 'user_correo', 
                    us.telefono as 'user_telefono', 
                    us.rol as 'user_rol', 
                    cli.ci as 'cliente_ci',
                    cli.nombre as 'cliente_nombre',
                    cli.telefono as 'cliente_telefono',
                    cli.email as 'cliente_email'
                    FROM reserva re, lotes lo, users us, clientes cli
                    WHERE
                    re.lote_id = lo.id AND
                    re.user_id = us.id AND
                    re.cliente_id = cli.id;
                ";
        $reservas = $this->mysqli->query($sql);
        return $reservas;
    }

    function mostrarTodasReservasPorFecha($fechaIni, $fechaFin){
        $sql = " SELECT  lo.*,
                    re.id as 'reserva_id',
                    re.comentario,
                    re.estado,
                    re.fecha_duracion,
                    re.fecha_creacion,
                    re.fecha_baja,
                    re.fecha_deposito,
                    re.fecha_liberacion,
                    us.nombre as 'user_nombre', 
                    us.correo as 'user_correo', 
                    us.telefono as 'user_telefono', 
                    us.rol as 'user_rol', 
                    cli.ci as 'cliente_ci',
                    cli.nombre as 'cliente_nombre',
                    cli.telefono as 'cliente_telefono',
                    cli.email as 'cliente_email'
                    FROM reserva re, lotes lo, users us, clientes cli
                    WHERE
                    re.lote_id = lo.id AND
                    re.user_id = us.id AND
                    re.cliente_id = cli.id AND
                    re.fecha_creacion >= '$fechaIni 00:00:00' AND
                    re.fecha_creacion <= '$fechaFin 23:59:59';
                ";
        $reservas = $this->mysqli->query($sql);
        return $reservas;
    }

    function mostrar($id) {
        $sql = "SELECT * FROM reserva WHERE id = '$id'";
        $reserva = $this->mysqli->query($sql);
        return $reserva;
    }

    function agregar(string $comentario, $userId, $clienteId, $loteId, $fechaDuracion){
        $sql = "INSERT INTO reserva(comentario, user_id, cliente_id, lote_id, estado, fecha_duracion, fecha_creacion, fecha_baja)
                VALUES('$comentario', '$userId', '$clienteId', '$loteId', 1, '$fechaDuracion', now(), now())";
        $result = $this->mysqli->query($sql);
        return $result;
    }

    function eliminar($id) {
        $sql = "UPDATE reserva SET estado=0, fecha_baja=now() WHERE id = '$id'";
        $userDeleted = $this->mysqli->query($sql);
        return $userDeleted;
    }

    function eliminarWithLoteId($loteId) {
        $sql = "UPDATE reserva SET estado=0, fecha_baja=now() WHERE lote_id = '$loteId'";
        $userDeleted = $this->mysqli->query($sql);
        return $userDeleted;
    }

    function actualizarFlujoDeposito($id, $fechaDuracion){ //La reserva pasa al estado DEPOSITO
        $sql = "UPDATE reserva SET fecha_duracion='$fechaDuracion', fecha_deposito=now(), fecha_liberacion=now() WHERE id = '$id'";
        $this->mysqli->query($sql);
    }

    function actualizarFlujoLiberar($id, $fechaDuracion){ //Se libera el deposito, pasa al estado RESERVADO
        $sql = "UPDATE reserva SET fecha_duracion='$fechaDuracion', fecha_liberacion=now() WHERE id = '$id'";
        $this->mysqli->query($sql);
    }
}