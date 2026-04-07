<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Enrollment;

interface IEnrollmentRepository
{
    public function getAllWithDetails(): array;
    public function getGenderStatsByCourse(int $courseId): array;
    public function findByStudentAndCourse(string $studentId, int $courseId): ?Enrollment;
    public function save(Enrollment $enrollment): bool;
    public function delete(int $id): bool;
}
