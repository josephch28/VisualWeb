<?php
    include_once "Conexion.php";
    $cedula = $_GET['estcedula'];
    $nombre = $_POST['estnombre'];
    $apellido = $_POST['estapellido'];
    $direccion = $_POST['estdireccion'];
    $telefono = $_POST['esttelefono'];
    $sexo = $_POST['estsexo'];
    $sqlUpdate = "UPDATE estudiantes SET estnombre='$nombre', 
                                        estapellido='$apellido', 
                                        estdireccion='$direccion', 
                                        esttelefono='$telefono', 
                                        estsexo='$sexo' WHERE estcedula='$cedula'";

    if ($conexion->query($sqlUpdate) === TRUE) {        
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false, 'errorMsg'=>"Error al actualizar el registro: " . $conexion->error]);
    }
?>