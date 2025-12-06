<?php
    require_once('../fpdf186/fpdf.php');
    require_once('../Models/Conexion.php');

    $curid = isset($_GET['curid']) ? $_GET['curid'] : 0;

    // Get Course Name
    $sqlCurso = "SELECT curnombre FROM cursos WHERE curid = '$curid'";
    $resCurso = $conexion->query($sqlCurso);
    $nombreCurso = "Desconocido";
    if($resCurso && $rowC = $resCurso->fetch_object()){
        $nombreCurso = $rowC->curnombre;
    }

    class PDF extends FPDF {
        public $curso;
        function Header() {
            $this->SetFont('Arial','B',15);
            $this->Cell(0,10,utf8_decode('Universidad Técnica de Ambato'),0,1,'C');
            $this->SetFont('Arial','B',12);
            $this->Cell(0,10,utf8_decode('Reporte de Estudiantes por Curso'),0,1,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(0,10,'Curso: ' . utf8_decode($this->curso),0,1,'C');
            $this->Ln(5);

            $this->SetFillColor(63, 81, 181);
            $this->SetTextColor(255);
            $this->SetFont('Arial','B',10);
            $this->Cell(30,10,utf8_decode('Cédula'),1,0,'C',true);
            $this->Cell(60,10,'Nombre Completo',1,0,'C',true);
            $this->Cell(60,10,utf8_decode('Dirección'),1,0,'C',true);
            $this->Cell(40,10,utf8_decode('Teléfono'),1,1,'C',true);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    // Get Students in Course
    $sqlStudents = "SELECT e.estcedula, e.estnombre, e.estapellido, e.estdireccion, e.esttelefono 
                    FROM matriculas m
                    INNER JOIN estudiantes e ON m.estudiante = e.estcedula
                    WHERE m.curso = '$curid'
                    ORDER BY e.estapellido, e.estnombre";
    $result = $conexion->query($sqlStudents);

    $pdf = new PDF('P','mm','A4');
    $pdf->curso = $nombreCurso;
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);

    $fill = false;
    if($result && $result->num_rows > 0){
        while($row = $result->fetch_object()){
            $pdf->SetFillColor(245,245,245);
            $pdf->Cell(30,10,$row->estcedula,1,0,'C',$fill);
            $pdf->Cell(60,10,utf8_decode($row->estapellido . ' ' . $row->estnombre),1,0,'L',$fill);
            $pdf->Cell(60,10,utf8_decode($row->estdireccion),1,0,'L',$fill);
            $pdf->Cell(40,10,$row->esttelefono,1,1,'C',$fill);
            $fill = !$fill;
        }
    } else {
        $pdf->Cell(0,10,'No hay estudiantes matriculados en este curso.',1,1,'C');
    }

    $pdf->Output();
?>
