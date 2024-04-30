<?php
require_once "../services/login_service.php";
$loginService = new LoginService();

header('Content-Type: application/json');
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $request = json_decode(file_get_contents("php://input"), true);
    $loginService->logout();
    http_response_code(200);
    echo json_encode([
        "status" => 200
    ]);
}