<?php

namespace App\Application\UseCases;

use App\Domain\Entities\Student;
use App\Domain\Repositories\IStudentRepository;

class StudentService
{
    private IStudentRepository $studentRepository;

    public function __construct(IStudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function getAll(): array
    {
        return $this->studentRepository->getAll();
    }

    public function create(array $data): array
    {
        if (empty($data['id_card']) || empty($data['first_name'])) {
            return ['success' => false, 'errorMsg' => 'Fields cannot be empty.'];
        }

        $student = new Student(
            $data['id_card'],
            $data['first_name'],
            $data['last_name'],
            $data['address'],
            $data['phone'],
            $data['gender']
        );

        // Optional: Check if exists
        if ($this->studentRepository->findByIdCard($data['id_card'])) {
            return ['success' => false, 'errorMsg' => 'Student ID already exists.'];
        }

        $success = $this->studentRepository->save($student);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Some errors occurred.'];
        }
    }

    public function update(string $idCard, array $data): array
    {
        $student = new Student(
            $idCard,
            $data['first_name'],
            $data['last_name'],
            $data['address'],
            $data['phone'],
            $data['gender']
        );

        $success = $this->studentRepository->update($student);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Some errors occurred.'];
        }
    }

    public function delete(string $idCard): array
    {
        $success = $this->studentRepository->delete($idCard);

        if ($success) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errorMsg' => 'Some errors occurred.'];
        }
    }
}
