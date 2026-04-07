<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Enrollment;
use App\Domain\Repositories\IEnrollmentRepository;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLEnrollmentRepository implements IEnrollmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function getAllWithDetails(): array
    {
        $query = "SELECT m.id, e.first_name, e.last_name, c.name as course_name 
                  FROM enrollments m 
                  INNER JOIN students e ON m.student_id = e.id_card 
                  INNER JOIN courses c ON m.course_id = c.id";
        $stmt = $this->db->query($query);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'id' => $row['id'],
                'firstName' => $row['first_name'],
                'lastName' => $row['last_name'],
                'courseName' => $row['course_name']
            ];
        }
        return $results;
    }

    public function getGenderStatsByCourse(int $courseId): array
    {
        $stmt = $this->db->prepare("SELECT e.gender, COUNT(*) as amount FROM enrollments m INNER JOIN students e ON m.student_id = e.id_card WHERE m.course_id = ? GROUP BY e.gender");
        $stmt->execute([$courseId]);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'gender' => $row['gender'],
                'amount' => $row['amount']
            ];
        }
        return $results;
    }

    public function findByStudentAndCourse(string $studentId, int $courseId): ?Enrollment
    {
        $stmt = $this->db->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
        $stmt->execute([$studentId, $courseId]);
        $row = $stmt->fetch();
        if ($row) {
            return new Enrollment($row['student_id'], $row['course_id'], $row['id']);
        }
        return null;
    }

    public function save(Enrollment $enrollment): bool
    {
        $stmt = $this->db->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
        return $stmt->execute([
            $enrollment->getStudentId(),
            $enrollment->getCourseId()
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM enrollments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
