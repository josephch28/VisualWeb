<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Course;
use App\Domain\Repositories\ICourseRepository;

class CourseService
{
    private ICourseRepository $courseRepository;

    public function __construct(ICourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAll(): array
    {
        return $this->courseRepository->getAll();
    }

    public function create(array $data): array
    {
        if (empty($data['name'])) {
            return ['success' => false, 'errorMsg' => 'Course name is required.'];
        }

        $course = new Course($data['name']);
        
        $success = $this->courseRepository->save($course);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Error saving course.'];
        }
    }

    public function update(int $id, array $data): array
    {
        if (empty($data['name'])) {
            return ['success' => false, 'errorMsg' => 'Course name is required.'];
        }

        $course = new Course($data['name'], $id);
        
        $success = $this->courseRepository->update($course);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Error updating course.'];
        }
    }

    public function delete(int $id): array
    {
        $success = $this->courseRepository->delete($id);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Error deleting course.'];
        }
    }
}
