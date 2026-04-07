<?php

namespace App\Core;

use App\Presentation\Controllers\StudentController;
use App\Presentation\Controllers\CourseController;
use App\Presentation\Controllers\EnrollmentController;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\ReportController;

class Router
{
    private array $routes = [];

    public function add(string $route, array $action)
    {
        $this->routes[$route] = $action;
    }

    public function dispatch(string $route)
    {
        if (array_key_exists($route, $this->routes)) {
            $action = $this->routes[$route];
            
            $controllerClass = $action[0];
            $method = $action[1];
            
            $controller = $this->resolveController($controllerClass);
            
            if (method_exists($controller, $method)) {
                $controller->$method();
            } else {
                echo json_encode(['error' => "Method {$method} not found in {$controllerClass}"]);
            }
        } else {
            echo json_encode(['error' => "Route {$route} not found"]);
        }
    }

    private function resolveController(string $controllerClass)
    {
        switch ($controllerClass) {
            case \App\Presentation\Controllers\StudentController::class:
                $repo = new \App\Infrastructure\Repositories\MySQLStudentRepository();
                $service = new \App\Application\UseCases\StudentService($repo);
                return new \App\Presentation\Controllers\StudentController($service);

            case \App\Presentation\Controllers\CourseController::class:
                $repo = new \App\Infrastructure\Repositories\MySQLCourseRepository();
                $service = new \App\Application\UseCases\CourseService($repo);
                return new \App\Presentation\Controllers\CourseController($service);

            case \App\Presentation\Controllers\EnrollmentController::class:
                $repo = new \App\Infrastructure\Repositories\MySQLEnrollmentRepository();
                $service = new \App\Application\UseCases\EnrollmentService($repo);
                return new \App\Presentation\Controllers\EnrollmentController($service);

            case \App\Presentation\Controllers\AuthController::class:
                $repo = new \App\Infrastructure\Repositories\MySQLUserRepository();
                $service = new \App\Application\UseCases\AuthService($repo);
                return new \App\Presentation\Controllers\AuthController($service);

            case \App\Presentation\Controllers\ReportController::class:
                $estRepo = new \App\Infrastructure\Repositories\MySQLStudentRepository();
                $curRepo = new \App\Infrastructure\Repositories\MySQLCourseRepository();
                $matRepo = new \App\Infrastructure\Repositories\MySQLEnrollmentRepository();
                $service = new \App\Application\UseCases\ReportService($estRepo, $curRepo, $matRepo);
                return new \App\Presentation\Controllers\ReportController($service);

            default:
                throw new \Exception("Controller not configured in factory");
        }
    }
}
