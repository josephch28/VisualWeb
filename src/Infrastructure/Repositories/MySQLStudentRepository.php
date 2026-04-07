<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Student;
use App\Domain\Repositories\IStudentRepository;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLStudentRepository implements IStudentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM students");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'idCard' => $row['id_card'],
                'firstName' => $row['first_name'],
                'lastName' => $row['last_name'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'gender' => $row['gender']
            ];
        }
        return $results;
    }

    public function getAllSorted(): array
    {
        $stmt = $this->db->query("SELECT * FROM students ORDER BY last_name, first_name");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'idCard' => $row['id_card'],
                'firstName' => $row['first_name'],
                'lastName' => $row['last_name'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'gender' => $row['gender']
            ];
        }
        return $results;
    }

    public function getByCourse(int $courseId): array
    {
        $stmt = $this->db->prepare("SELECT e.* FROM enrollments m INNER JOIN students e ON m.student_id = e.id_card WHERE m.course_id = ? ORDER BY e.last_name, e.first_name");
        $stmt->execute([$courseId]);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'idCard' => $row['id_card'],
                'firstName' => $row['first_name'],
                'lastName' => $row['last_name'],
                'address' => $row['address'],
                'phone' => $row['phone'],
                'gender' => $row['gender']
            ];
        }
        return $results;
    }

    public function findByIdCard(string $idCard): ?Student
    {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id_card = ?");
        $stmt->execute([$idCard]);
        $row = $stmt->fetch();
        if ($row) {
            return new Student($row['id_card'], $row['first_name'], $row['last_name'], $row['address'], $row['phone'], $row['gender']);
        }
        return null;
    }

    public function save(Student $student): bool
    {
        $stmt = $this->db->prepare("INSERT INTO students (id_card, first_name, last_name, address, phone, gender) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $student->getIdCard(),
            $student->getFirstName(),
            $student->getLastName(),
            $student->getAddress(),
            $student->getPhone(),
            $student->getGender()
        ]);
    }

    public function update(Student $student): bool
    {
        $stmt = $this->db->prepare("UPDATE students SET first_name = ?, last_name = ?, address = ?, phone = ?, gender = ? WHERE id_card = ?");
        return $stmt->execute([
            $student->getFirstName(),
            $student->getLastName(),
            $student->getAddress(),
            $student->getPhone(),
            $student->getGender(),
            $student->getIdCard()
        ]);
    }

    public function delete(string $idCard): bool
    {
        $stmt = $this->db->prepare("DELETE FROM students WHERE id_card = ?");
        return $stmt->execute([$idCard]);
    }
}
