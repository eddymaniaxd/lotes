<?php
require_once "../services/historial_migracion_service.php";
require_once "../services/login_service.php";
require_once "../enums/roles_enum.php";

$historialMigracionService = new HistorialMigracionService();
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

$user = $_SESSION["user"];
if($user["rol"] == RolesEnum::CLIENTE->value){
    http_response_code(403);
    echo json_encode([
        "status" => 403,
        "message" => "Usuario sin permisos para este recurso"
    ]);
    return;
}

if($_SERVER["REQUEST_METHOD"] === "GET"){ //Obtener un usuario
    if($id){ //Verificar el ID
        $result = $historialMigracionService->mostrar($id);
    }
    else{ //Devuelve todos los historiales
        $result = $historialMigracionService->index();
    }
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $result->rows
    ]);
}