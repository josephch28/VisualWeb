<?php

namespace App\Presentation\Controllers;

use App\Application\UseCases\StudentService;

class StudentController
{
    private StudentService $service;

    public function __construct(StudentService $service)
    {
        $this->service = $service;
    }

    public function get()
    {
        $data = $this->service->getAll();
        
        $search = $_POST['id_card'] ?? ($_POST['student_id'] ?? '');
        if (!empty($search)) {
            $data = array_filter($data, function($item) use ($search) {
                return stripos($item['idCard'], $search) !== false;
            });
            $data = array_values($data);
        }
        
        echo json_encode($data);
    }

    public function create()
    {
        $data = [
            'id_card' => $_POST['estcedula'] ?? '',  // Mapping from old frontend input names for now
            'first_name' => $_POST['estnombre'] ?? '',
            'last_name' => $_POST['estapellido'] ?? '',
            'address' => $_POST['estdireccion'] ?? '',
            'phone' => $_POST['esttelefono'] ?? '',
            'gender' => $_POST['estsexo'] ?? ''
        ];
        
        // Let's accept both English and Spanish POST keys in case frontend hasn't updated yet or to be robust
        if (isset($_POST['id_card'])) {
            $data['id_card'] = $_POST['id_card'];
            $data['first_name'] = $_POST['first_name'];
            $data['last_name'] = $_POST['last_name'];
            $data['address'] = $_POST['address'];
            $data['phone'] = $_POST['phone'];
            $data['gender'] = $_POST['gender'];
        }

        $result = $this->service->create($data);
        echo json_encode($result);
    }

    public function update()
    {
        $idCard = $_GET['id_card'] ?? ($_GET['estcedula'] ?? '');
        $data = [
            'first_name' => $_POST['estnombre'] ?? ($_POST['first_name'] ?? ''),
            'last_name' => $_POST['estapellido'] ?? ($_POST['last_name'] ?? ''),
            'address' => $_POST['estdireccion'] ?? ($_POST['address'] ?? ''),
            'phone' => $_POST['esttelefono'] ?? ($_POST['phone'] ?? ''),
            'gender' => $_POST['estsexo'] ?? ($_POST['gender'] ?? '')
        ];
        
        $result = $this->service->update($idCard, $data);
        echo json_encode($result);
    }

    public function delete()
    {
        $idCard = $_POST['id_card'] ?? ($_POST['estcedula'] ?? '');
        $result = $this->service->delete($idCard);
        echo json_encode($result);
    }
}
