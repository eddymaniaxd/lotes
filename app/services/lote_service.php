<?php
require_once "../models/lote.php";
require_once "../models/historial_migracion.php";
require_once "../enums/estado_lotes_enum.php";
require_once "../services/reserva_service.php";

class LoteService {

    private $lote;
    private $historialMigracion;
    private $reserva;

    function __construct()
    {
        $this->lote = new Lote();
        $this->historialMigracion = new HistorialMigracion();
        $this->reserva = new ReservaService();
    }

    function index() {
        return $this->lote->index();
    }

    function mostrar($id) {
        return $this->lote->mostrar($id);
    }

    function buscarPorUV($uv) {
        return $this->lote->buscarPorUV($uv);
    }

    function actualizarEstado($id, $request) {
        $estadoLote = $request["estado"];
        try{
            $this->lote->actualizarEstado($id, $estadoLote);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function buscarLoteLibre($idLote) {
        $loteLibre = $this->lote->buscarLoteLibre($idLote);
        if($loteLibre->num_rows > 0) return true;
        return false;
    }

    function buscarLoteUVMznLote($uv, $manzano, $lote) {
        return $this->lote->buscarLoteUVMznLote($uv, $manzano, $lote);
    }

    function actualizarEstadoAPI() {
        $apiUrl = 'http://159.69.163.254/ServicioLotesTest/api/ObtenerLotesTest?Proyecto=Las%20Barreras%201';
        $jsonData = file_get_contents($apiUrl);
    
        // Convertir el JSON a un array asociativo
        $lotes = json_decode($jsonData, true);
        $lotesActualizados = [];
        foreach($lotes as $lote) {
            $ItemCode = $lote["ItemCode"];
            // $Proyectos = $lote["Proyectos"];
            // $UV = $lote["UV"];
            // $Manzado = $lote["Manzado"];
            // $Lote = $lote["Lote"];
            $Estado = $lote["Estado"];
            // $Superficie = $lote["Superficie"];
            // $Modelo = $lote["Modelo"];
            // $Precio = $lote["Precio"];
            // $Cuota = $lote["Cuota"];
            
            $loteActualizar = $this->lote->buscarPorItemCode($ItemCode);

            $idHistorialMigracion = -1;

            if($loteActualizar && $loteActualizar->num_rows > 0){

                $estadoLoteActualizar = $loteActualizar->rows[0]["Estado"];

                if($Estado == EstadoLotes::VENDIDO->value){

                    if( $estadoLoteActualizar != EstadoLotes::VENDIDO->value){
                        $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                        $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                    }
                }
                elseif($Estado == EstadoLotes::BLOQUEADO->value){

                    if($estadoLoteActualizar != EstadoLotes::BLOQUEADO->value){
                        $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                        $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                    }
                }
                elseif($Estado == EstadoLotes::TRANSFERIDO->value){
                    
                    if( $estadoLoteActualizar != EstadoLotes::TRANSFERIDO->value){
                            $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                            $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                        }
                }
                elseif($Estado == EstadoLotes::DEPOSITO->value){

                    if( $estadoLoteActualizar != EstadoLotes::DEPOSITO->value){
                            $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                            $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                        }
                }
                elseif($Estado == EstadoLotes::RESERVADO->value){
                    
                    if( $estadoLoteActualizar == EstadoLotes::LIBRE->value ){
                        $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                        $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                    }
                }
                elseif($Estado == EstadoLotes::LIBRE->value){
                    
                    if( $estadoLoteActualizar != EstadoLotes::RESERVADO->value &&
                        $estadoLoteActualizar != EstadoLotes::DEPOSITO->value &&
                        $estadoLoteActualizar != EstadoLotes::LIBRE->value ){
                        $this->lote->actualizarEstadoWithItemCode($ItemCode, $Estado);
                        
                        $idHistorialMigracion = $this->historialMigracion->agregar($ItemCode, $estadoLoteActualizar, $Estado);
                        $this->reserva->eliminarWithLoteId($loteActualizar->rows[0]["id"]);
                    }
                }

                if($idHistorialMigracion != -1){
                    $resultHistorialMigracion = $this->historialMigracion->mostrar($idHistorialMigracion);
                    if($resultHistorialMigracion && $resultHistorialMigracion->num_rows > 0){
                        array_push($lotesActualizados, $resultHistorialMigracion->rows[0]);
                    }
                }
            }
    
        }

        return $lotesActualizados;
    }
    
    function actualizarLoteWithItemCode(){ //Actualizar datos de la tabla lote desde la API
        try {
            $apiUrl = 'http://159.69.163.254/ServicioLotesTest/api/ObtenerLotesTest?Proyecto=Las%20Barreras%201';
            $jsonData = file_get_contents($apiUrl);
        
            // Convertir el JSON a un array asociativo
            $lotes = json_decode($jsonData, true);
            
            foreach($lotes as $lote) {
                $ItemCode = $lote["ItemCode"];
                $Proyectos = $lote["Proyectos"];
                $UV = $lote["UV"];
                $Manzado = $lote["Manzado"];
                $Lote = $lote["Lote"];
                $Estado = $lote["Estado"];
                $Superficie = $lote["Superficie"];
                $Modelo = $lote["Modelo"];
                $Precio = $lote["Precio"];
                $Cuota = $lote["Cuota"];
                
                $loteActualizar = $this->lote->buscarPorItemCode($ItemCode);

                if ($loteActualizar && $loteActualizar->num_rows > 0){
                    if( $Estado == EstadoLotes::LIBRE->value){
                        $this->lote->actualizarLoteWithItemCode($ItemCode, $Proyectos, $UV, $Manzado, $Lote, $Estado, $Superficie, $Modelo, $Precio, $Cuota);
                        $this->reserva->eliminarWithLoteId($loteActualizar->rows[0]["id"]);
                    }else{
                        $this->lote->actualizarLoteWithItemCode($ItemCode, $Proyectos, $UV, $Manzado, $Lote, $Estado, $Superficie, $Modelo, $Precio, $Cuota);
                    }
                }
        
            }
            return true;
        } catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function buscarPorItemCode($itemCode) {
        return $this->lote->buscarPorItemCode($itemCode);
    }
}