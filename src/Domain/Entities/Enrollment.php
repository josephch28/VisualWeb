<?php

namespace App\Domain\Entities;

class Enrollment
{
    private ?int $id;
    private string $studentId;
    private int $courseId;

    public function __construct(string $studentId, int $courseId, ?int $id = null)
    {
        $this->studentId = $studentId;
        $this->courseId = $courseId;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentId(): string
    {
        return $this->studentId;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }
}
