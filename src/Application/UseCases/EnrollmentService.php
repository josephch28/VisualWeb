<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Enrollment;
use App\Domain\Repositories\IEnrollmentRepository;

class EnrollmentService
{
    private IEnrollmentRepository $enrollmentRepository;

    public function __construct(IEnrollmentRepository $enrollmentRepository)
    {
        $this->enrollmentRepository = $enrollmentRepository;
    }

    public function getAll(): array
    {
        // For UI we usually need details
        return $this->enrollmentRepository->getAllWithDetails();
    }

    public function create(array $data): array
    {
        if (empty($data['student_id']) || empty($data['course_id'])) {
            return ['success' => false, 'errorMsg' => 'Student and Course are required.'];
        }

        // Check if already enrolled to prevent duplicates
        $existing = $this->enrollmentRepository->findByStudentAndCourse($data['student_id'], $data['course_id']);
        if ($existing) {
            return ['success' => false, 'errorMsg' => 'The student is already enrolled in this course.'];
        }

        $enrollment = new Enrollment($data['student_id'], (int)$data['course_id']);
        
        $success = $this->enrollmentRepository->save($enrollment);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Error saving enrollment.'];
        }
    }

    public function delete(int $id): array
    {
        $success = $this->enrollmentRepository->delete($id);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Error deleting enrollment.'];
        }
    }
}
