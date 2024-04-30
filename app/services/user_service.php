<?php
require_once "../models/user.php";

class UserService {

    private $user;

    function __construct()
    {
        $this->user = new User();
    }

    function index() {
        return $this->user->index();
    }

    function mostrar($id) {
        $users = $this->user->mostrar($id);
        return $users;
    }

    function agregar($request) {
        
        try{
            $nombre = $request["nombre"];
            $telefono = $request["telefono"];
            $correo = $request["correo"];
            $password = $request["password"];
            $rol = $request["rol"];
            $this->user->agregar($nombre, $telefono, $correo, $password, $rol);
            return true;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function editar() {

    }

    function actualizar($id, $request) {
        try{
            $nombre = $request["nombre"];
            $telefono = $request["telefono"];
            $correo = $request["correo"];
            $password = $request["password"];
            
            $this->user->actualizar($id, $nombre, $telefono, $correo, $password);
            return true;    
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function eliminar($id) {
        try{
            $this->user->eliminar($id);
            return true;    
        }catch(Exception $e){
            throw new Exception($e->getMessage());
            return false;
        }
    }

    function excelUsuarios() {

        $users = $this->user->index();
        $excelData = '';

        $excelData = "ID"."\t"."Nombre"."\t"."Teléfono"."\t"."Correo"."\t"."Role";
        $excelData .= "\n";
        
        foreach($users->rows as $user){
            $excelData .= $user["id"]."\t".$user["nombre"]."\t".$user["telefono"]."\t".$user["correo"]."\t".$user["rol"];
            $excelData .= "\n";
        }

        return $excelData;
    }
}