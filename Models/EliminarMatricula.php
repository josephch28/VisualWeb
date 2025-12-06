<?php
    include_once "Conexion.php";
    
    $id = $_POST['id'];
    
    $sqlDelete = "DELETE FROM matriculas WHERE id=$id";
    
    if ($conexion->query($sqlDelete) === TRUE) {
        echo json_encode(array('success'=>true));
    } else {
        echo json_encode(array('errorMsg'=>'Error al eliminar la matrícula.'));
    }
?>
