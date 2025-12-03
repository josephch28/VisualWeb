<?php
    include_once "Conexion.php";
    $cedula = isset($_POST['estcedula']) ? $_POST['estcedula'] : '';
    
    if ($cedula != "") {
        $sqlSelect = "SELECT * FROM estudiantes WHERE estcedula LIKE '%$cedula%'";
    } else {
        $sqlSelect = "SELECT * FROM estudiantes";
    }
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
