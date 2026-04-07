<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Course;

interface ICourseRepository
{
    public function getAll(): array;
    public function getByStudent(string $idCard): array;
    public function findById(int $id): ?Course;
    public function save(Course $course): bool;
    public function update(Course $course): bool;
    public function delete(int $id): bool;
}
