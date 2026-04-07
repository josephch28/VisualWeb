<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Student;

interface IStudentRepository
{
    public function getAll(): array;
    public function getAllSorted(): array;
    public function getByCourse(int $courseId): array;
    public function findByIdCard(string $idCard): ?Student;
    public function save(Student $student): bool;
    public function update(Student $student): bool;
    public function delete(string $idCard): bool;
}
