<?php
    $server = "localhost";
    $database = "cuartouta";
    $username = "root";
    $password = "";
    $conexion = mysqli_connect($server, $username, $password, $database);
    $mysqli = new mysqli($server, $username, $password, $database);
    if(!$mysqli){
        die("Fallo la conexion a la base de datos: " . mysqli_connect_error());
    }
?>