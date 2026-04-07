<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\IStudentRepository;
use App\Domain\Repositories\ICourseRepository;
use App\Domain\Repositories\IEnrollmentRepository;

class ReportService
{
    private IStudentRepository $studentRepo;
    private ICourseRepository $courseRepo;
    private IEnrollmentRepository $enrollmentRepo;

    public function __construct(
        IStudentRepository $studentRepo,
        ICourseRepository $courseRepo,
        IEnrollmentRepository $enrollmentRepo
    ) {
        $this->studentRepo = $studentRepo;
        $this->courseRepo = $courseRepo;
        $this->enrollmentRepo = $enrollmentRepo;
    }

    public function getGeneralReportData(): array
    {
        return $this->studentRepo->getAllSorted();
    }

    public function getCourseReportData(int $courseId): array
    {
        $course = $this->courseRepo->findById($courseId);
        $students = $this->studentRepo->getByCourse($courseId);

        return [
            'courseName' => $course ? $course->getName() : 'Unknown',
            'students' => $students
        ];
    }

    public function getStudentDetailData(string $idCard): array
    {
        $student = $this->studentRepo->findByIdCard($idCard);
        if (!$student) {
            return [];
        }

        $courses = $this->courseRepo->getByStudent($idCard);

        return [
            'student' => [
                'idCard' => $student->getIdCard(),
                'names' => $student->getFirstName() . ' ' . $student->getLastName(),
                'address' => $student->getAddress(),
                'phone' => $student->getPhone(),
                'gender' => $student->getGender()
            ],
            'courses' => $courses
        ];
    }

    public function getGenderChartData(int $courseId): array
    {
        $course = $this->courseRepo->findById($courseId);
        $stats = $this->enrollmentRepo->getGenderStatsByCourse($courseId);

        $males = 0;
        $females = 0;

        foreach ($stats as $stat) {
            $gender = strtoupper($stat['gender']);
            if ($gender == 'M' || $gender == 'MASCULINO' || $gender == 'MALE') $males += $stat['amount'];
            if ($gender == 'F' || $gender == 'FEMENINO' || $gender == 'FEMALE') $females += $stat['amount'];
        }

        return [
            'courseName' => $course ? $course->getName() : 'Unknown',
            'males' => $males,
            'females' => $females,
            'total' => $males + $females
        ];
    }
}
