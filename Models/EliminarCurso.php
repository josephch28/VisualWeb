<?php
    include_once "Conexion.php";
    
    $curid = $_POST['curid'];
    
    $sqlDelete = "DELETE FROM cursos WHERE curid=$curid";
    
    if ($conexion->query($sqlDelete) === TRUE) {
        echo json_encode(array('success'=>true));
    } else {
        echo json_encode(array('errorMsg'=>'Error al eliminar el curso.'));
    }
?>
