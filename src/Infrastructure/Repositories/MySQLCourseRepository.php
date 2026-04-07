<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Course;
use App\Domain\Repositories\ICourseRepository;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLCourseRepository implements ICourseRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM courses");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
        return $results;
    }

    public function getByStudent(string $idCard): array
    {
        $stmt = $this->db->prepare("SELECT c.* FROM enrollments m INNER JOIN courses c ON m.course_id = c.id WHERE m.student_id = ?");
        $stmt->execute([$idCard]);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'id' => $row['id'],
                'name' => $row['name']
            ];
        }
        return $results;
    }

    public function findById(int $id): ?Course
    {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            return new Course($row['name'], $row['id']);
        }
        return null;
    }

    public function save(Course $course): bool
    {
        $stmt = $this->db->prepare("INSERT INTO courses (name) VALUES (?)");
        return $stmt->execute([$course->getName()]);
    }

    public function update(Course $course): bool
    {
        $stmt = $this->db->prepare("UPDATE courses SET name = ? WHERE id = ?");
        return $stmt->execute([
            $course->getName(),
            $course->getId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
