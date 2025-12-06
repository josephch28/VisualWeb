<?php
    include_once "Conexion.php";
    $curnombre = isset($_POST['curnombre']) ? $_POST['curnombre'] : '';
    
    if ($curnombre != "") {
        $sqlSelect = "SELECT * FROM cursos WHERE curnombre LIKE '%$curnombre%'";
    } else {
        $sqlSelect = "SELECT * FROM cursos";
    }
    $respuesta = $conexion->query($sqlSelect);
    $resultado = array();
    if($respuesta -> num_rows > 0){
        while($fila = $respuesta->fetch_array()){
            array_push($resultado, $fila);
        }
    }else{
        // Return empty array instead of string to avoid JS errors in EasyUI
        // $resultado = "No hay registros"; 
    }
    print_r(json_encode($resultado));
?>
