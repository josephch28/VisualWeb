<?php
    include_once "Conexion.php";
    $cedula = $_POST['estcedula'];
    $nombre = $_POST['estnombre'];
    $apellido = $_POST['estapellido'];
    $direccion = $_POST['estdireccion'];
    $telefono = $_POST['esttelefono'];
    $sexo = $_POST['estsexo'];
    $sqlCheck = "SELECT * FROM estudiantes WHERE estcedula = '$cedula'";
    $check = $conexion->query($sqlCheck);
    
    if ($check && $check->num_rows > 0) {
        $result = array('errorMsg' => 'Ya existe un estudiante con esa Cédula.');
        echo json_encode($result);
    } else {
        $sqlInsert = "INSERT INTO estudiantes(estcedula, estnombre, estapellido, estdireccion, esttelefono, estsexo) 
                      VALUES ('$cedula', '$nombre', '$apellido', '$direccion', '$telefono', '$sexo')";
        if ($conexion->query($sqlInsert) === TRUE) {        
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('errorMsg' => 'Error al guardar el registro.'));
        }
    }
?>