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

    // Get Gender Count
    $sqlGender = "SELECT e.estsexo, COUNT(*) as cantidad 
                  FROM matriculas m
                  INNER JOIN estudiantes e ON m.estudiante = e.estcedula
                  WHERE m.curso = '$curid'
                  GROUP BY e.estsexo";
    $resGender = $conexion->query($sqlGender);
    
    $males = 0;
    $females = 0;
    
    while($row = $resGender->fetch_object()){
        if(strtoupper($row->estsexo) == 'M' || strtoupper($row->estsexo) == 'MASCULINO') $males += $row->cantidad;
        if(strtoupper($row->estsexo) == 'F' || strtoupper($row->estsexo) == 'FEMENINO') $females += $row->cantidad;
    }
    
    $total = $males + $females;
    // Prevent division by zero
    $malePct = ($total > 0) ? round(($males / $total) * 100, 1) : 0;
    $femalePct = ($total > 0) ? round(($females / $total) * 100, 1) : 0;

    class PDF extends FPDF {
        public $curso;
        function Header() {
            $this->SetFont('Arial','B',15);
            $this->Cell(0,10,utf8_decode('Universidad Técnica de Ambato'),0,1,'C');
            $this->SetFont('Arial','B',12);
            $this->Cell(0,10,utf8_decode('Estadísticas de Género por Curso'),0,1,'C');
            $this->SetFont('Arial','',10);
            $this->Cell(0,10,'Curso: ' . utf8_decode($this->curso),0,1,'C');
            $this->Ln(10);
        }
        
        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    $pdf = new PDF('P','mm','A4');
    $pdf->curso = $nombreCurso;
    $pdf->AliasNbPages();
    $pdf->AddPage();

    if($total == 0){
        $pdf->SetFont('Arial','I',12);
        $pdf->Cell(0,10,'No hay datos para graficar.',0,1,'C');
    } else {
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(0,10,"Total de estudiantes: $total",0,1,'C');
        $pdf->Ln(10);
        
        // Chart Config
        $chartX = 60;
        $chartY = 80;
        $barWidth = 40;
        $maxHeight = 100;
        
        // Axis Lines
        $pdf->Line($chartX - 10, $chartY + $maxHeight, $chartX + 100, $chartY + $maxHeight); // X Axis
        $pdf->Line($chartX - 10, $chartY, $chartX - 10, $chartY + $maxHeight); // Y Axis
        
        // MALE Bar (Blue)
        $barHeightM = ($malePct / 100) * $maxHeight;
        $pdf->SetFillColor(50, 50, 200);
        $pdf->Rect($chartX, $chartY + ($maxHeight - $barHeightM), $barWidth, $barHeightM, 'F');
        $pdf->SetXY($chartX, $chartY + $maxHeight + 2);
        $pdf->Cell($barWidth, 10, "Masculino ($malePct%)", 0, 0, 'C');
        $pdf->SetXY($chartX, $chartY + ($maxHeight - $barHeightM) - 10);
        $pdf->Cell($barWidth, 10, $males, 0, 0, 'C'); // Show count above bar
        
        // FEMALE Bar (Pink)
        $barHeightF = ($femalePct / 100) * $maxHeight;
        $pdf->SetFillColor(200, 50, 150);
        $pdf->Rect($chartX + $barWidth + 10, $chartY + ($maxHeight - $barHeightF), $barWidth, $barHeightF, 'F');
        $pdf->SetXY($chartX + $barWidth + 10, $chartY + $maxHeight + 2);
        $pdf->Cell($barWidth, 10, "Femenino ($femalePct%)", 0, 0, 'C');
        $pdf->SetXY($chartX + $barWidth + 10, $chartY + ($maxHeight - $barHeightF) - 10);
        $pdf->Cell($barWidth, 10, $females, 0, 0, 'C'); // Show count above bar
    }

    $pdf->Output();
?>
