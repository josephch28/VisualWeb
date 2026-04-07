<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\ReportService;
use FPDF;

class ReportController
{
    private ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
        require_once __DIR__ . '/../../../fpdf186/fpdf.php';
    }

    public function generateGeneral()
    {
        $data = $this->service->getGeneralReportData();

        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(80);
        $pdf->Cell(120, 10, utf8_decode('Universidad Técnica de Ambato'), 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80);
        $pdf->Cell(120, 10, 'General Student Report', 0, 0, 'C');
        $pdf->Ln(20);

        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 10, 'ID Card', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'First Name', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Last Name', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Address', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Phone', 1, 0, 'C', true);
        $pdf->Cell(20, 10, 'Gender', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0);

        $fill = false;
        foreach ($data as $row) {
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell(30, 10, $row['idCard'], 1, 0, 'L', $fill);
            $pdf->Cell(40, 10, utf8_decode($row['firstName']), 1, 0, 'L', $fill);
            $pdf->Cell(40, 10, utf8_decode($row['lastName']), 1, 0, 'L', $fill);
            $pdf->Cell(60, 10, utf8_decode($row['address']), 1, 0, 'L', $fill);
            $pdf->Cell(30, 10, $row['phone'], 1, 0, 'L', $fill);
            $pdf->Cell(20, 10, $row['gender'], 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->Output();
    }

    public function generateByCourse()
    {
        $courseId = (int)($_GET['course_id'] ?? ($_GET['curid'] ?? 0));
        $data = $this->service->getCourseReportData($courseId);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 10, utf8_decode('Universidad Técnica de Ambato'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Course Student Report', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Course: ' . utf8_decode($data['courseName']), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 10, 'ID Card', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Full Name', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Address', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Phone', 1, 1, 'C', true);

        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 10);
        $fill = false;

        if (count($data['students']) > 0) {
            foreach ($data['students'] as $row) {
                $pdf->SetFillColor(245, 245, 245);
                $pdf->Cell(30, 10, $row['idCard'], 1, 0, 'C', $fill);
                $pdf->Cell(60, 10, utf8_decode($row['firstName'] . ' ' . $row['lastName']), 1, 0, 'L', $fill);
                $pdf->Cell(60, 10, utf8_decode($row['address']), 1, 0, 'L', $fill);
                $pdf->Cell(40, 10, $row['phone'], 1, 1, 'C', $fill);
                $fill = !$fill;
            }
        } else {
            $pdf->Cell(0, 10, 'No students enrolled in this course.', 1, 1, 'C');
        }

        $pdf->Output();
    }

    public function generateStudentDetail()
    {
        $idCard = $_GET['id_card'] ?? ($_GET['estcedula'] ?? '');
        $data = $this->service->getStudentDetailData($idCard);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 10, utf8_decode('Universidad Técnica de Ambato'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Student Detail File', 0, 1, 'C');
        $pdf->Ln(10);

        if (empty($data)) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Student not found.', 0, 1, 'C');
            $pdf->Output();
            return;
        }

        $est = $data['student'];
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->Cell(0, 10, 'Personal Data', 1, 1, 'L', true);
        $pdf->SetTextColor(0);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'ID Card:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, $est['idCard'], 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'Full Names:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, utf8_decode($est['names']), 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'Address:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, utf8_decode($est['address']), 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'Phone:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, $est['phone'], 0, 1);

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 10, 'Gender:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, $est['gender'], 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetFillColor(63, 81, 181);
        $pdf->SetTextColor(255);
        $pdf->Cell(0, 10, 'Enrolled Courses', 1, 1, 'L', true);
        $pdf->SetTextColor(0);

        if (count($data['courses']) > 0) {
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(30, 10, 'Course ID', 1, 0, 'C', true);
            $pdf->Cell(0, 10, 'Course Name', 1, 1, 'L', true);
            $pdf->SetFont('Arial', '', 10);
            foreach ($data['courses'] as $rowC) {
                $pdf->Cell(30, 10, $rowC['id'], 1, 0, 'C');
                $pdf->Cell(0, 10, utf8_decode($rowC['name']), 1, 1, 'L');
            }
        } else {
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(0, 10, 'The student is not enrolled in any course.', 1, 1, 'C');
        }

        $pdf->Output();
    }

    public function generateGenderChart()
    {
        $courseId = (int)($_GET['course_id'] ?? ($_GET['curid'] ?? 0));
        $data = $this->service->getGenderChartData($courseId);

        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(0, 10, utf8_decode('Universidad Técnica de Ambato'), 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Gender Statistics by Course', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Course: ' . utf8_decode($data['courseName']), 0, 1, 'C');
        $pdf->Ln(10);

        if ($data['total'] == 0) {
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(0, 10, 'No data to chart.', 0, 1, 'C');
        } else {
            $malePct = round(($data['males'] / $data['total']) * 100, 1);
            $femalePct = round(($data['females'] / $data['total']) * 100, 1);

            $pdf->SetFont('Arial', '', 11);
            $pdf->Cell(0, 10, "Total Students: " . $data['total'], 0, 1, 'C');
            $pdf->Ln(10);

            $chartX = 60;
            $chartY = 80;
            $barWidth = 40;
            $maxHeight = 100;

            $pdf->Line($chartX - 10, $chartY + $maxHeight, $chartX + 100, $chartY + $maxHeight);
            $pdf->Line($chartX - 10, $chartY, $chartX - 10, $chartY + $maxHeight);

            // MALE Bar (Blue)
            $barHeightM = ($malePct / 100) * $maxHeight;
            $pdf->SetFillColor(50, 50, 200);
            $pdf->Rect($chartX, $chartY + ($maxHeight - $barHeightM), $barWidth, $barHeightM, 'F');
            $pdf->SetXY($chartX, $chartY + $maxHeight + 2);
            $pdf->Cell($barWidth, 10, "Male ($malePct%)", 0, 0, 'C');
            $pdf->SetXY($chartX, $chartY + ($maxHeight - $barHeightM) - 10);
            $pdf->Cell($barWidth, 10, $data['males'], 0, 0, 'C');

            // FEMALE Bar (Pink)
            $barHeightF = ($femalePct / 100) * $maxHeight;
            $pdf->SetFillColor(200, 50, 150);
            $pdf->Rect($chartX + $barWidth + 10, $chartY + ($maxHeight - $barHeightF), $barWidth, $barHeightF, 'F');
            $pdf->SetXY($chartX + $barWidth + 10, $chartY + $maxHeight + 2);
            $pdf->Cell($barWidth, 10, "Female ($femalePct%)", 0, 0, 'C');
            $pdf->SetXY($chartX + $barWidth + 10, $chartY + ($maxHeight - $barHeightF) - 10);
            $pdf->Cell($barWidth, 10, $data['females'], 0, 0, 'C');
        }

        $pdf->Output();
    }
}
