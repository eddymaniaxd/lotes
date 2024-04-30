<?php
// namespace conexiondb\config\conexion;
require_once("../../env.php");

class DbMySQL {

    private ?object $conexion;

    function __construct()
    {
        try{
            $this->conexion = mysqli_connect(
                DB_SERVER,
                DB_USER,
                DB_PASSWORD,
                DB_DATABASE
            );
        }catch(\mysqli_sql_exception $e){
            throw new  \Exception("No se puede conectar a la base de datos, " . $e->getMessage());
        }
    }

    function query(string $sql) {
        try{
            $query = $this->conexion->query($sql);
            if($query instanceof \mysqli_result){
                $data = [];
                while($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }
                $result = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->rows = $data;
                $query->close();
                unset($data);
                return $result;
            }else{
                return true;
            }
        }catch(\mysqli_sql_exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    function queryWithReturnID(string $sql) {
        try{
            $query = $this->conexion->query($sql);
            if($query === TRUE){
                $idInserted = $this->conexion->insert_id;
                return $idInserted;
            }
            return null;
        }catch(\mysqli_sql_exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    function __destruct()
    {
        if ($this->conexion) {
			$this->conexion->close();

			$this->conexion = null;
		}
    }
}

