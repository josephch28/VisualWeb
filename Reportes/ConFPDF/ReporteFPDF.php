<?php
require_once 'fpdf/fpdf.php';
require_once '../../Models/conexion.php';

$sqlSelect = "SELECT * FROM estudiantes";
$resultado = $conn->query($sqlSelect);
$fpdf = new FPDF();

$fpdf->AddPage();
$fpdf-> setTitle ("Estudiante Report");
$fpdf->SetFont("Arial", "B", 8);
$fpdf->Cell(20, 10, "Cedula");
$fpdf->Cell(40, 10, "Nombre");
while ($row = $resultado->fetch_array()) {
    $fpdf->Ln(10);
    $fpdf->Cell(20, 10, $row['estcedula'],1);
    $fpdf->Cell(40, 10, $row['estnombre'],1);
}

$fpdf->OutPut(); 





?>