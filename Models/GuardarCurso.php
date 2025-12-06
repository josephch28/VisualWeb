<?php
    include_once "Conexion.php";
    
    $curnombre = $_POST['curnombre'];
    
    $sqlInsert = "INSERT INTO cursos(curnombre) VALUES ('$curnombre')";
    
    if ($conexion->query($sqlInsert) === TRUE) {
        echo json_encode(array('success'=>true));
    } else {
        echo json_encode(array('errorMsg'=>'Error al guardar el curso.'));
    }
?>
