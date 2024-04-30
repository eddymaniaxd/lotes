<?php
require_once "../services/lote_service.php";
require_once "../services/login_service.php";
require_once "../enums/roles_enum.php";

$loteService = new LoteService();
$loginService = new LoginService();

//Obtener un usuario
header('Content-Type: application/json');

if(!$loginService->veriricarAutenticacion()){
    http_response_code(401);
    echo json_encode([
        "status" => 401,
        "message" => "Sin autorización"
    ]);
    return;
}

if($_SERVER["REQUEST_METHOD"] === "GET"){
    $id = $_GET["id"];
    
    $uv = $_GET["uv"];
    $manzano = $_GET["manzano"];
    $lote = $_GET["lote"];

    $itemcode = $_GET["itemcode"];

    if($id){
        $result = $loteService->mostrar($id);
    }elseif($uv && $manzano && $lote){
        $result = $loteService->buscarLoteUVMznLote($uv, $manzano, $lote);
        
    }
    elseif($uv){
        $result = $loteService->buscarPorUV($uv);
    }
    elseif($itemcode){
        $result = $loteService->buscarPorItemCode($itemcode);
    }
    else{
        $result = $loteService->index();
    }
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $result->rows
    ]);
}
elseif($_SERVER["REQUEST_METHOD"] === "PUT"){

    $user = $_SESSION["user"];
    if($user["rol"] == RolesEnum::CLIENTE->value){
        http_response_code(403);
        echo json_encode([
            "status" => 403,
            "message" => "Usuario sin permisos para este recurso"
        ]);
        return;
    }
    $request = json_decode(file_get_contents("php://input"), true);

    $type = $request["type"];
    
    if($type == "update"){
        $lotesActualizados = $loteService->actualizarLoteWithItemCode();
        http_response_code(200);
        echo json_encode([
            "status" => 200,
            "message" => "Lotes actualizados correctamente"
        ]);
    }else{
        $lotesActualizados = $loteService->actualizarEstadoAPI();
        http_response_code(200);
        echo json_encode([
            "status" => 200,
            "data" => $lotesActualizados
        ]);
    }
    // if($lotesActualizados){
    //     http_response_code(200);
    //     echo json_encode([
    //         "status" => 200,
    //         "data" => $lotesActualizados
    //     ]);
    // }else{
    //     http_response_code(400);
    //     echo json_encode([
    //         "status" => 400,
    //         "message" => "Sin resultados"
    //     ]);
    // }
}