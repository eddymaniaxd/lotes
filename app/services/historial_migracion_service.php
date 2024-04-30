<?php
require_once "../models/historial_migracion.php";

class HistorialMigracionService {

    private $historialMigracion;

    function __construct()
    {
        $this->historialMigracion = new HistorialMigracion();
    }

    function index() {
        return $this->historialMigracion->index();
    }

    function mostrar($id) {
        $historial = $this->historialMigracion->mostrar($id);
        return $historial;
    }
}