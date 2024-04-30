<?php
require_once "../services/reserva_service.php";
require_once "../services/login_service.php";
require_once "../services/lote_service.php";
require_once "../enums/estado_lotes_enum.php";
require_once "../enums/roles_enum.php";

$reservaService = new ReservaService();
$loginService = new LoginService();
$loteService = new LoteService();

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

if($_SERVER["REQUEST_METHOD"] === "GET"){ //Obtener reserva
    $id = $_GET["id"];
    $fechaIni = $_GET["fechaIni"];
    $fechaFin = $_GET["fechaFin"];
    $report = $_GET["report"];
    if($id){
        $result = $reservaService->mostrar($id);
    }
    elseif($fechaIni && $fechaFin){
        $result = $reservaService->mostrarTodasReservasPorFecha($fechaIni, $fechaFin);
    }
    elseif($report && $report == "xls"){
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=historicos.xls");
        http_response_code(201);
        echo $reservaService->excelReserva();
        return;
    }else{
        $result = $reservaService->index();
    }
    http_response_code(200);
    echo json_encode([
        "status" => 200,
        "data" => $result->rows
    ]);
}
elseif($_SERVER["REQUEST_METHOD"] === "POST"){ //Agregar reserva
    $request = json_decode(file_get_contents("php://input"), true);
    if(!validarFormularioReserva($request)) return;
    
    $loteId = $request["loteId"];
    $isLoteLibre = $loteService->buscarLoteLibre($loteId);
    if(!$isLoteLibre) {
        echo json_encode([
            "status" => 400,
            "message" => "El lote no se encuentra libre"
        ]);
        return;
    }

    $result = $reservaService->agregar($request);
    
    if($result){
        http_response_code(201);
        echo json_encode([
            "status" => 201
        ]);
    }else{
        echo json_encode([
            "status" => 400,
            "message" => "No se puedo realizar la reserva"
        ]);  
    }
}
elseif($_SERVER["REQUEST_METHOD"] === "PUT") { //Cambiar el estado de un reserva - lote
    // if($user["rol"] == RolesEnum::CLIENTE->value){
    //     http_response_code(403);
    //     echo json_encode([
    //         "status" => 403,
    //         "message" => "Usuario sin permisos para este recurso"
    //     ]);
    //     return;
    // }
    $id = $_GET["id"]; //Recuperar el id de la reserva
    if($id){
        $request = json_decode(file_get_contents("php://input"), true);
        if(!isEstadoValido($request["estado"])){
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Estado no válido"
            ]);
            return;
        }
        $resultActualizarFlujo = $reservaService->actualizarFlujo($id, $request);
        if($resultActualizarFlujo){
            http_response_code(200);
            echo json_encode([
                "status" => 200
            ]);
        }else{
            http_response_code(404);
            echo json_encode([
                "status" => 404,
                "message" => "Reserva no encontrado" 
            ]);
        }
    }
}
elseif($_SERVER["REQUEST_METHOD"] === "DELETE"){ //Eliminar reserva
    // if($user["rol"] == RolesEnum::CLIENTE->value){
    //     http_response_code(403);
    //     echo json_encode([
    //         "status" => 403,
    //         "message" => "Usuario sin permisos para este recurso"
    //     ]);
    //     return;
    // }
    $id = $_GET["id"];
    if($id){
        $buscarReserva = $reservaService->mostrar($id);
        if($buscarReserva && $buscarReserva->num_rows > 0){
            $userAuth = $_SESSION["user"];
            if($userAuth["rol"] == RolesEnum::ADMIN->value){
                $result = $reservaService->eliminar($id);
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
            elseif($userAuth["rol"] == RolesEnum::CLIENTE->value){
                $userIdReserva = $buscarReserva->rows[0]["user_id"];
                if($userIdReserva == $userAuth["id"]){
                    $result = $reservaService->eliminar($id);
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
        }
    }
}

function validarFormularioReserva($request) {
    //$ci       = $request["cliente"]["ci"];
    $nombre   = $request["cliente"]["nombre"];
    $telefono = $request["cliente"]["telefono"];

    // if($ci == null){
    //     http_response_code(400);
    //     echo json_encode([
    //         "status" => 400,
    //         "field" => "ci",
    //         "message" => "Campo ci requerido"
    //     ]);
    //     return false;
    // }
    // else
    if($nombre == null){
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "nombre",
            "message" => "Campo nombre requerido"
        ]);
        return false;
    }
    elseif($telefono == null) {
        http_response_code(400);
        echo json_encode([
            "status" => 400,
            "field" => "telefono",
            "message" => "Campo telefono requerido"
        ]);
        return false;
    }
    return true;
}

function isEstadoValido($estado){
    if(
        $estado == EstadoLotes::VENDIDO->value ||
        $estado == EstadoLotes::LIBRE->value ||
        $estado == EstadoLotes::RESERVADO->value ||
        $estado == EstadoLotes::DEPOSITO->value ||
        $estado == EstadoLotes::TRANSFERIDO->value
    ){
        return true;
    }
    return false;
}