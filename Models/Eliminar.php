<?php
    include_once "Conexion.php";
    $cedula = $_POST['estcedula'];
    $sqlDelete = "DELETE FROM estudiantes WHERE estcedula = '$cedula'";
    if ($conexion->query($sqlDelete) === TRUE) {        
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'errorMsg'=>"Error al eliminar el registro: " . $conexion->error]);
    }
?>