<?php
require_once "../services/login_service.php";
$loginService = new LoginService();

header('Content-Type: application/json');
if(!$loginService->veriricarAutenticacion()){
    http_response_code(401);
    echo json_encode([
        "status" => 401,
        "message" => "Sin autorización"
    ]);
}else{
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $_SESSION["user"]
    ]);
}