<?php
require_once "../services/login_service.php";
$loginService = new LoginService();

//------------------Ingresar
header('Content-Type: application/json');
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $request = json_decode(file_get_contents("php://input"), true);
    if(!validarCamposLogin($request)) return;
    if(!validarZona($request)) return;

    $result = $loginService->ingresar(
                                $request["correo"], 
                                $request["password"]
                            );

    if($result->num_rows > 0){
        $user = $result->rows[0];
        $_SESSION["user"] = $user;

        http_response_code(200);
        echo json_encode([
            "status" => 200,
            "data" => $user
        ]);
    }else{
        $loginService->logout();
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "message" => "Credenciales no válidas"
        ]);
    }
}elseif ($_SERVER['REQUEST_METHOD'] === 'GET') { //Method GET
    $isAutenticado = $loginService->veriricarAutenticacion();
    if($isAutenticado){
        http_response_code(200);
        echo json_encode([
            "status" => 200,
            "data" => $_SESSION["user"]
        ]);
    }else{
        http_response_code(401);
        echo json_encode([
            "status" => 401,
            "message" => "Sin permisos"
        ]);
    }
}

function validarCamposLogin($request) {
    $correo = $request["correo"];
    $password = $request["password"];

    if($correo == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "correo",
            "message" => "Campo correo requerido"
        ]);
        return false;
    }
    elseif($password == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "password",
            "message" => "Campo password requerido"
        ]);
        return false;
    }
    return true;
}

function validarZona($request){
    $zona = $request["zona"];
    if($zona == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "Zona no seleccionada"
        ]);
        return false;
    }
    elseif($zona != "las-barreras-1"){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "Zona no válida"
        ]);
        return false;
    }
    return true;
}