<?php
    include_once "Conexion.php";
    
    $estudiante = $_POST['estudiante'];
    $curso = $_POST['curso'];
    
    // Check for duplicates
    $sqlCheck = "SELECT * FROM matriculas WHERE estudiante = '$estudiante' AND curso = $curso";
    $resultCheck = $conexion->query($sqlCheck);
    
    if ($resultCheck && $resultCheck->num_rows > 0) {
        echo json_encode(array('errorMsg'=>'El estudiante ya está matriculado en este curso.'));
    } else {
        $sqlInsert = "INSERT INTO matriculas(estudiante, curso) VALUES ('$estudiante', $curso)";
        
        if ($conexion->query($sqlInsert) === TRUE) {
            echo json_encode(array('success'=>true));
        } else {
            echo json_encode(array('errorMsg'=>'Error al guardar la matrícula.'));
        }
    }
?>
