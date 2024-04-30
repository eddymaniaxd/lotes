<?php
require_once "../services/reserva_service.php";
require_once "../services/login_service.php";

$reservaService = new ReservaService();
$loginService = new LoginService();

header('Content-Type: application/json');
if(!$loginService->veriricarAutenticacion()){
    http_response_code(401);
    echo json_encode([
        "status" => 401,
        "message" => "Sin autorización"
    ]);
    return;
}
if($_SERVER["REQUEST_METHOD"] === "GET"){ //Obtener reserva
    $id = $_GET["id"];
    if($id){
        $result = $reservaService->mostrar($id);
    }else{
        $result = $reservaService->mostrarTodasReservas();
    }
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $result->rows
    ]);
}