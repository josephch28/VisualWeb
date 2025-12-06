<?php
    include_once "Conexion.php";
    
    $curid = $_REQUEST['curid'];
    $curnombre = $_POST['curnombre'];
    
    $sqlUpdate = "UPDATE cursos SET curnombre='$curnombre' WHERE curid=$curid";
    
    if ($conexion->query($sqlUpdate) === TRUE) {
        echo json_encode(array('success'=>true));
    } else {
        echo json_encode(array('errorMsg'=>'Error al actualizar el curso.'));
    }
?>
