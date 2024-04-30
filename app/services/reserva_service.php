<?php
require_once  "../models/reserva.php";
require_once  "../models/cliente.php";
require_once  "../models/lote.php";
require_once "../enums/estado_lotes_enum.php";
require_once "../enums/time_reservas_enum.php";

class ReservaService {

    private $reserva;
    private $cliente;
    private $lote;

    function __construct()
    {
        $this->reserva = new Reserva();
        $this->cliente = new Cliente();
        $this->lote = new Lote();
    }

    function index() {
        return $this->reserva->index();
    }

    function mostrarTodasReservas(){
        return $this->reserva->mostrarTodasReservas();
    }

    function mostrarTodasReservasPorFecha($fechaIni, $fechaFin){
        return $this->reserva->mostrarTodasReservasPorFecha($fechaIni, $fechaFin);
    }

    function mostrar($id) {
        $reserva = $this->reserva->mostrar($id);
        return $reserva;
    }

    function agregar($request) {
        try{
            $clienteRequest = $request["cliente"];
            $userId = $request["user"]["id"];
            $loteId = $request["loteId"];
            $comentario = $request["cliente"]["comentario"];

            $ci = $clienteRequest["ci"];
            $nombre = $clienteRequest["nombre"];
            $telefono = $clienteRequest["telefono"];
            $email = $clienteRequest["email"];

            $clientId = $this->cliente->agregarAndReturnID($ci, $nombre, $telefono, $email); //Registrar cliente
            if($clientId){
                $fechaDuracion = $this->getFechaDuracion("PT".TimeReservas::HRS_RESERVAS->value."H");
                $this->reserva->agregar($comentario, $userId, $clientId, $loteId, $fechaDuracion); //Registrar la reserva
                $this->lote->actualizarEstado($loteId, "RESERVADO"); // Actualizar el estado del lote a reservado
                return true;
            }else{
                return false;
            }

        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function eliminar($id) {
        try{
            $reserva = $this->mostrar($id);
            if($reserva->num_rows > 0){
                $idLote = $reserva->rows[0]["lote_id"];
                $this->lote->actualizarEstado($idLote, "LIBRE");
                
                $this->reserva->eliminar($id);
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function eliminarWithLoteId($loteId) {
        try{
            $this->reserva->eliminarWithLoteId($loteId);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function actualizarFlujo($id, $request){
        try{
            $reserva = $this->mostrar($id);
            if($reserva->num_rows > 0){
                $estado = $request["estado"];
                $loteId = $reserva->rows[0]["lote_id"];

                if( $estado == EstadoLotes::DEPOSITO->value ){
                    $fechaDuracionDeposito = $this->getFechaDuracion("P".TimeReservas::DAY_DEPOSITO->value."D");
                    $this->reserva->actualizarFlujoDeposito($id, $fechaDuracionDeposito);
                }
                elseif( $estado == EstadoLotes::RESERVADO->value ){
                    $fechaDuracionReserva = $this->getFechaDuracion("PT".TimeReservas::HRS_RESERVAS->value."H");
                    $this->reserva->actualizarFlujoLiberar($id, $fechaDuracionReserva);
                }
                $this->lote->actualizarEstado($loteId, $estado); //Actualizar el estado del lote

                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function excelReserva() {
        $reservas = $this->reserva->mostrarTodasReservas();
        $excelData = '';

        $excelData = "UV"."\t"."Manzano"."\t"."Lote"."\t"."Estado Lote"."\t"."Vendedor"."\t"."Nombre Cliente"."\t"."Telefono Cliente"."\t"."Correo Cliente"."\t"."Comentario"."\t"."Fecha creación"."\t"."Fecha baja"."\t"."Fecha deposito"."\t"."Fecha liberación";
        $excelData .= "\n";
        
        foreach($reservas->rows as $reserva){
            $excelData .= $reserva["UV"]."\t".$reserva["Manzado"]."\t".$reserva["Lote"]."\t".$reserva["Estado"]."\t".$reserva["user_nombre"]."\t".$reserva["cliente_nombre"]."\t".$reserva["cliente_telefono"]."\t".$reserva["cliente_correo"]."\t".$reserva["comentario"]."\t".$reserva["fecha_creacion"]."\t".$reserva["fecha_baja"]."\t".$reserva["fecha_deposito"]."\t".$reserva["fecha_liberacion"];
            $excelData .= "\n";
        }

        return $excelData;
    }

    function getFechaDuracion($dateIntervalo){
        date_default_timezone_set('America/La_Paz'); 
        $fechaActual = date("Y-m-d H:i:s");

        $objFechaActual = new DateTime($fechaActual);
        $objFechaActual->add(new DateInterval($dateIntervalo));

        $resultFecha = $objFechaActual->format('Y-m-d H:i:s');

        return $resultFecha;
    }
}