<?php
    require_once('fpdf186/fpdf.php');
    require_once('Models/Conexion.php');
    $sqlSelect = "SELECT * FROM estudiantes";
    $result = $conexion->query($sqlSelect);

    //Construccion del PDF 
    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();
    $pdf->SetTitle('Reporte de Estudiantes');
    $pdf->SetFont('Arial','B',10);
    
    $pdf->Cell(30,10,'Cedula');
    $pdf->Cell(40,10,'Nombre');
    $pdf->Cell(60,10,'Apellido');
    $pdf->Cell(40,10,'Direccion');
    $pdf->Cell(40,10,'Telefono');
    $pdf->Cell(40,10,'Sexo');
    $pdf->Ln();
    while($row = $result->fetch_object()){
        $cedula = $row->estcedula;
        $nombre = $row->estnombre;
        $apellido = $row->estapellido;
        $direccion = $row->estdireccion;
        $telefono = $row->esttelefono;
        $sexo = $row->estsexo;
        $pdf->Cell(30,10,$cedula,1);
        $pdf->Cell(40,10,$nombre,1);
        $pdf->Cell(60,10,$apellido,1);
        $pdf->Cell(40,10,$direccion,1);
        $pdf->Cell(40,10,$telefono,1);
        $pdf->Cell(40,10,$sexo,1);
        $pdf->Ln();
    }

    $pdf->Output();
?>