<?php
    include_once "Conexion.php";
    $sqlSelect = "SELECT * FROM estudiantes ";
    $respuesta = $conexion->query($sqlSelect);
    $resultado = array();
    if($respuesta -> num_rows > 0){
        while($fila = $respuesta->fetch_array()){
            array_push($resultado, $fila);
        }
    }else{
        $resultado = "No hay registros";
    }
    print_r(json_encode($resultado));
?>  