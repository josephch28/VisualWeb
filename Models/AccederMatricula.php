<?php
    include_once "Conexion.php";
    
    // Join with students and courses to get names
    // Table: matriculas (id, estudiante, curso)
    // estudiante -> estudiantes.estcedula
    // curso -> cursos.curid
    
    $sqlSelect = "SELECT m.id, e.estcedula, e.estnombre, e.estapellido, c.curid, c.curnombre 
                  FROM matriculas m 
                  INNER JOIN estudiantes e ON m.estudiante = e.estcedula 
                  INNER JOIN cursos c ON m.curso = c.curid";
                  
    $respuesta = $conexion->query($sqlSelect);
    $resultado = array();
    if($respuesta && $respuesta -> num_rows > 0){
        while($fila = $respuesta->fetch_array()){
            array_push($resultado, $fila);
        }
    }
    print_r(json_encode($resultado));
?>
