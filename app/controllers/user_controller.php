<?php
require_once "../services/user_service.php";
require_once "../services/login_service.php";
require_once "../enums/roles_enum.php";

$userService = new UserService();
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
    $id = $_GET["id"];
    $reportXLS  = $_GET["report"];

    if($id){ //Devuelve un usuario por el id
        $result = $userService->mostrar($id);
    }
    elseif($reportXLS && $reportXLS == "xls"){ //Reporte xls de usuarios
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=usuarios.xls");
        http_response_code(200);
        echo $userService->excelUsuarios();
        return;
    }
    else{ //Devuelve todos los usuarios
        $result = $userService->index();
    }
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $result->rows
    ]);
}
elseif($_SERVER["REQUEST_METHOD"] === "POST"){ //Agregar usuario
    $request = json_decode(file_get_contents("php://input"), true);
    if(!validarFormulario($request)) return;
    $result = $userService->agregar($request);

    if($result){
        http_response_code(201);
        echo json_encode([
            "status" => 201
        ]);
    }else{
        http_response_code(400);
    }
}
elseif($_SERVER["REQUEST_METHOD"] === "PUT"){ //Actualizar usuario
    $id = $_GET["id"];
    if($id){
        $request = json_decode(file_get_contents("php://input"), true);
        $result = $userService->actualizar($id, $request);

        if($result){
            http_response_code(200);
            echo json_encode([
                "status" => 200
            ]);
        }
    }
}
elseif($_SERVER["REQUEST_METHOD"] === "DELETE"){ //Eliminar usuario
    $id = $_GET["id"];
    if($id){
        $result = $userService->eliminar($id);
        if($result){
            http_response_code(204);
            echo json_encode([
                "status" => 204
            ]);
        }else{
            http_response_code(404);
            echo json_encode([
                "status" => 404,
                "message" => "Recurso no encontrado"
            ]);
        }
    }
}

function validarFormulario($request) {
    $nombre   = $request["nombre"];
    $telefono = $request["telefono"];
    $correo = $request["correo"];
    $password = $request["password"];

    if($nombre == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "nombre",
            "message" => "Campo nombre requerido"
        ]);
        return false;
    }
    elseif($telefono == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "telefono",
            "message" => "Campo telefono requerido"
        ]);
        return false;
    }
    elseif($correo == null) {
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "correo",
            "message" => "Campo correo requerido"
        ]);
        return false;
    }
    elseif($password == null) {
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