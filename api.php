<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Router;
use App\Presentation\Controllers\StudentController;
use App\Presentation\Controllers\CourseController;
use App\Presentation\Controllers\EnrollmentController;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\ReportController;

$router = new Router();

// Students
$router->add('students/get', [StudentController::class, 'get']);
$router->add('students/create', [StudentController::class, 'create']);
$router->add('students/update', [StudentController::class, 'update']);
$router->add('students/delete', [StudentController::class, 'delete']);

// Courses
$router->add('courses/get', [CourseController::class, 'get']);
$router->add('courses/create', [CourseController::class, 'create']);
$router->add('courses/update', [CourseController::class, 'update']);
$router->add('courses/delete', [CourseController::class, 'delete']);

// Enrollments
$router->add('enrollments/get', [EnrollmentController::class, 'get']);
$router->add('enrollments/create', [EnrollmentController::class, 'create']);
$router->add('enrollments/delete', [EnrollmentController::class, 'delete']);

// Auth
$router->add('auth/login', [AuthController::class, 'login']);
$router->add('auth/logout', [AuthController::class, 'logout']);

// Reports
$router->add('reports/general', [ReportController::class, 'generateGeneral']);
$router->add('reports/course', [ReportController::class, 'generateByCourse']);
$router->add('reports/student', [ReportController::class, 'generateStudentDetail']);
$router->add('reports/gender', [ReportController::class, 'generateGenderChart']);

$route = $_GET['route'] ?? '';
$router->dispatch($route);
