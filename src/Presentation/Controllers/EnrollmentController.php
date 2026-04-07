<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\EnrollmentService;

class EnrollmentController
{
    private EnrollmentService $service;

    public function __construct(EnrollmentService $service)
    {
        $this->service = $service;
    }

    public function get()
    {
        $data = $this->service->getAll();
        echo json_encode($data);
    }

    public function create()
    {
        $data = [
            'student_id' => $_POST['student_id'] ?? ($_POST['estudiante'] ?? ''),
            'course_id' => $_POST['course_id'] ?? ($_POST['curso'] ?? 0)
        ];
        $result = $this->service->create($data);
        echo json_encode($result);
    }

    public function delete()
    {
        $id = (int)($_POST['id'] ?? 0);
        $result = $this->service->delete($id);
        echo json_encode($result);
    }
}
