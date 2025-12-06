<?php
    require_once('../fpdf186/fpdf.php');
    require_once('../Models/Conexion.php');
    $sqlSelect = "SELECT * FROM estudiantes";
    $result = $conexion->query($sqlSelect);

    class PDF extends FPDF {
        function Header() {
            // Header Color
            $this->SetFillColor(230,230,230); // Light gray for header background
            
            $this->SetFont('Arial','B',15);
            $this->Cell(80);
            $this->Cell(120,10,utf8_decode('Universidad Técnica de Ambato'),0,0,'C');
            $this->Ln(10);
            
            $this->SetFont('Arial','B',12);
            $this->Cell(80);
            $this->Cell(120,10,'Reporte General de Estudiantes',0,0,'C');
            $this->Ln(20);
            
            // Table Header
            $this->SetFillColor(63, 81, 181); // Blue Indigo
            $this->SetTextColor(255); // White text
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Cédula'),1,0,'C',true);
            $this->Cell(40,10,'Nombre',1,0,'C',true);
            $this->Cell(40,10,'Apellido',1,0,'C',true);
            $this->Cell(60,10,utf8_decode('Dirección'),1,0,'C',true);
            $this->Cell(30,10,utf8_decode('Teléfono'),1,0,'C',true);
            $this->Cell(20,10,'Sexo',1,1,'C',true);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $sqlSelect = "SELECT * FROM estudiantes ORDER BY estapellido, estnombre";
    $result = $conexion->query($sqlSelect);

    $pdf = new PDF('L','mm','A4');
    $pdf->AliasNbPages(); // Required for {nb}
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(0); // Reset to black text
    
    $fill = false;
    
    while($row = $result->fetch_object()){
        $pdf->SetFillColor(245,245,245); // Very light gray for alternate rows
        $pdf->Cell(30,10,$row->estcedula,1,0,'L',$fill);
        $pdf->Cell(40,10,utf8_decode($row->estnombre),1,0,'L',$fill);
        $pdf->Cell(40,10,utf8_decode($row->estapellido),1,0,'L',$fill);
        $pdf->Cell(60,10,utf8_decode($row->estdireccion),1,0,'L',$fill);
        $pdf->Cell(30,10,$row->esttelefono,1,0,'L',$fill);
        $pdf->Cell(20,10,$row->estsexo,1,1,'C',$fill);
        $fill = !$fill;
    }

    $pdf->Output();
?>