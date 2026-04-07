<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\CourseService;

class CourseController
{
    private CourseService $service;

    public function __construct(CourseService $service)
    {
        $this->service = $service;
    }

    public function get()
    {
        $data = $this->service->getAll();
        
        $search = $_POST['name'] ?? ($_POST['course_name'] ?? '');
        if (!empty($search)) {
            $data = array_filter($data, function($item) use ($search) {
                return stripos($item['name'], $search) !== false;
            });
            $data = array_values($data); // re-index
        }
        
        echo json_encode($data);
    }

    public function create()
    {
        $data = [
            'name' => $_POST['name'] ?? ($_POST['curnombre'] ?? '')
        ];
        $result = $this->service->create($data);
        echo json_encode($result);
    }

    public function update()
    {
        $id = (int)($_GET['course_id'] ?? ($_GET['id'] ?? 0));
        $data = [
            'name' => $_POST['name'] ?? ($_POST['curnombre'] ?? '')
        ];
        $result = $this->service->update($id, $data);
        echo json_encode($result);
    }

    public function delete()
    {
        $id = (int)($_POST['course_id'] ?? ($_POST['id'] ?? 0));
        $result = $this->service->delete($id);
        echo json_encode($result);
    }
}
