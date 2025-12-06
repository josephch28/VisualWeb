<?php
    require_once('../fpdf186/fpdf.php');
    require_once('../Models/Conexion.php');

    $cedula = isset($_GET['estcedula']) ? $_GET['estcedula'] : '';

    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial','B',15);
            $this->Cell(0,10,utf8_decode('Universidad Técnica de Ambato'),0,1,'C');
            $this->SetFont('Arial','B',12);
            $this->Cell(0,10,utf8_decode('Ficha Detallada del Estudiante'),0,1,'C');
            $this->Ln(10);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new PDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    // Student Info
    $sqlEst = "SELECT * FROM estudiantes WHERE estcedula = '$cedula'";
    $resEst = $conexion->query($sqlEst);

    if($resEst && $row = $resEst->fetch_object()){
        $pdf->SetFont('Arial','B',11);
        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->Cell(0,10,utf8_decode('Datos Personales'),1,1,'L',true);
        $pdf->SetTextColor(0);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,utf8_decode('Cédula:'),0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,10,$row->estcedula,0,1);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,'Nombres:',0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,10,utf8_decode($row->estnombre . ' ' . $row->estapellido),0,1);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,utf8_decode('Dirección:'),0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,10,utf8_decode($row->estdireccion),0,1);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,utf8_decode('Teléfono:'),0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,10,$row->esttelefono,0,1);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(40,10,'Sexo:',0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(0,10,$row->estsexo,0,1);
        
        $pdf->Ln(10);
        
        // Courses Info
        $pdf->SetFont('Arial','B',11);
        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->Cell(0,10,utf8_decode('Cursos Matriculados'),1,1,'L',true);
        $pdf->SetTextColor(0);
        
        $sqlCursos = "SELECT c.curid, c.curnombre 
                      FROM matriculas m 
                      INNER JOIN cursos c ON m.curso = c.curid 
                      WHERE m.estudiante = '$cedula'";
        $resCursos = $conexion->query($sqlCursos);
        
        if($resCursos && $resCursos->num_rows > 0){
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(230,230,230);
            $pdf->Cell(30,10,'ID Curso',1,0,'C',true);
            $pdf->Cell(0,10,'Nombre del Curso',1,1,'L',true);
            $pdf->SetFont('Arial','',10);
            while($rowC = $resCursos->fetch_object()){
                $pdf->Cell(30,10,$rowC->curid,1,0,'C');
                $pdf->Cell(0,10,utf8_decode($rowC->curnombre),1,1,'L');
            }
        } else {
            $pdf->SetFont('Arial','I',10);
            $pdf->Cell(0,10,utf8_decode('El estudiante no está matriculado en ningún curso.'),1,1,'C');
        }

    } else {
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,'Estudiante no encontrado.',0,1,'C');
    }

    $pdf->Output();
?>
