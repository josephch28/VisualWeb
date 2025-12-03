<?php
    include_once "Conexion.php";
    $cedula = $_POST['estcedula'];
    $nombre = $_POST['estnombre'];
    $apellido = $_POST['estapellido'];
    $direccion = $_POST['estdireccion'];
    $telefono = $_POST['esttelefono'];
    $sexo = $_POST['estsexo'];
    $sqlInsert = "INSERT INTO estudiantes(estcedula, estnombre, estapellido, estdireccion, esttelefono, estsexo) 
                  VALUES ('$cedula', '$nombre', '$apellido', '$direccion', '$telefono', '$sexo')";
    if ($conexion->query($sqlInsert) === TRUE) {        
        echo json_encode("Registro guardado exitosamente.");
    } else {
        echo json_encode("Error al guardar el registro: " . $sqlInsert. $conexion->error);
    }
?>