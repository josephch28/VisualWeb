<?php
    session_start();
    include_once "Conexion.php";
    
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
    
    // Check connection
    if ($conexion->connect_error) {
        die(json_encode(array('errorMsg' => 'Connection failed: ' . $conexion->connect_error)));
    }

    // Protect against SQL injection
    $usuario = $conexion->real_escape_string($usuario);
    $contrasena = $conexion->real_escape_string($contrasena);
    
    $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
    $result = $conexion->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];
        
        echo json_encode(array(
            'success' => true,
            'usuario' => $row['usuario'],
            'rol' => $row['rol']
        ));
    } else {
        echo json_encode(array('errorMsg' => 'Usuario o contraseña incorrectos.'));
    }
?>
