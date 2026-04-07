<?php
session_start();
?>
<section class="container mt-4">
    <!-- Bootstrap CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Still using jQuery for AJAX convenience -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Select2 for Searchable Selects -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Select2 Bootstrap 5 compatibility fixes */
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
    </style>

    <div class="row mb-4">
        <div class="col-md-9">
            <h1>Gestión de Services (Bootstrap)</h1>
            <p>Administración universitaria con interfaz moderna.</p>
        </div>
        <div class="col-md-3 text-end" id="userInfo">
            <?php if (isset($_SESSION['user'])): ?>
                <span class="badge bg-secondary"><?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['rol']; ?>)</span>
                <button onclick="logout()" class="btn btn-outline-danger btn-sm ms-2">Cerrar Sesión</button>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if (!isset($_SESSION['user'])) {
        // --- NOT LOGGED IN: SHOW LOGIN MODAL ---
        ?>
        <!-- Static Backdrop Modal for Login -->
        <div class="modal fade show" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Login</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="loginUser" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="loginUser" placeholder="Ingrese user">
                        </div>
                        <div class="mb-3">
                            <label for="loginPass" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="loginPass" placeholder="Ingrese contraseña">
                        </div>
                        <div id="loginError" class="alert alert-danger d-none" role="alert"></div>
                    </div>
                    <div class="modal-footer">
                        <a href="index.php" class="btn btn-secondary">Salir</a> <!-- Navigation to Index -->
                        <button type="button" class="btn btn-primary" onclick="submitLogin()">Entrar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function submitLogin() {
                var u = $('#loginUser').val();
                var p = $('#loginPass').val();
                $.post('api.php?route=auth/login', { user: u, password: p }, function (res) {
                    var data = JSON.parse(res);
                    if (data.success) {
                        location.reload();
                    } else {
                        $('#loginError').text(data.errorMsg).removeClass('d-none');
                    }
                });
            }
        </script>
        <?php
    } else {
        // --- LOGGED IN: SHOW MAIN CONTENT ---
        $rol = $_SESSION['rol'];
        $isSecretary = (stripos($rol, 'Secretary') !== false);
        $isAdmin = (stripos($rol, 'Administrator') !== false);
        ?>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
            <?php if ($isSecretary || !$isAdmin): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="#students"
                        type="button" role="tab">Estudiantes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses" type="button"
                        role="tab">Cursos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="enrollments-tab" data-bs-toggle="tab" data-bs-target="#enrollments"
                        type="button" role="tab">Matrículas</button>
                </li>
            <?php endif; ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo ($isAdmin && !$isSecretary) ? 'active' : ''; ?>" id="reports-tab"
                    data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">Reportes PDF</button>
            </li>
        </ul>

        <div class="tab-content" id="mainTabsContent">

            <?php if ($isSecretary || !$isAdmin): ?>
                <!-- TAB ESTUDIANTES -->
                <div class="tab-pane fade show active" id="students" role="tabpanel">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" id="searchStudent" class="form-control" placeholder="Buscar por Cédula...">
                        </div>
                        <div class="col-md-8 text-end">
                            <button class="btn btn-primary" onclick="newStudent()"><i class="bi bi-person-plus"></i> Nuevo
                                Estudiante</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaStudents">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cédula</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Dirección</th>
                                    <th>Teléfono</th>
                                    <th>Sexo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB CURSOS -->
                <div class="tab-pane fade" id="courses" role="tabpanel">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" id="searchCourse" class="form-control" placeholder="Buscar por Nombre...">
                        </div>
                        <div class="col-md-8 text-end">
                            <button class="btn btn-primary" onclick="newCourse()"><i class="bi bi-plus-circle"></i> New
                                Course</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaCourses">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Curso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB MATRICULAS -->
                <div class="tab-pane fade" id="enrollments" role="tabpanel">
                    <div class="row mb-3 text-end">
                        <div class="col-12">
                            <button class="btn btn-success" onclick="newEnrollment()"><i class="bi bi-card-checklist"></i> New
                                Matrícula</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tablaEnrollments">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Estudiante</th>
                                    <th>Curso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TAB REPORTES -->
            <div class="tab-pane fade <?php echo ($isAdmin && !$isSecretary) ? 'show active' : ''; ?>" id="reports"
                role="tabpanel">
                <div class="card mx-auto" style="max-width: 600px;">
                    <div class="card-header bg-primary text-white">
                        Generación de Reportes
                    </div>
                    <div class="card-body">
                        <p class="card-text">Seleccione las opciones para descargar sus reportes en PDF.</p>

                        <div class="d-grid gap-2 mb-4 pb-3 border-bottom">
                            <label class="fw-bold">1. Reporte General</label>
                            <a href="api.php?route=reports/general" target="_blank" class="btn btn-outline-primary"><i
                                    class="bi bi-printer"></i> Generar Full List</a>
                        </div>

                        <div class="mb-4 pb-3 border-bottom">
                            <label class="fw-bold mb-2">2. Por Curso</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">ID Curso</span>
                                <input type="number" class="form-control" id="rep_course_id">
                            </div>
                            <div class="d-grid gap-2">
                                <button onclick="genCourseReport()" class="btn btn-outline-dark"><i
                                        class="bi bi-people"></i> Lista de Estudiantes</button>
                                <button onclick="genGenderChart()" class="btn btn-outline-info"><i
                                        class="bi bi-bar-chart"></i> Gráfico de Género</button>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="fw-bold mb-2">3. Por Estudiante</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Cédula</span>
                                <input type="text" class="form-control" id="rep_cedula">
                            </div>
                            <div class="d-grid gap-2">
                                <button onclick="genStudentReport()" class="btn btn-outline-success"><i
                                        class="bi bi-person-badge"></i> Ficha Detallada</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- MODALS FOR CRUD -->

        <!-- Modal Student -->
        <div class="modal fade" id="modalStudent" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalStudentTitle">Estudiante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formStudent">
                            <input type="hidden" id="student_mode" value="new">
                            <div class="mb-2"><label class="form-label">Cédula</label><input type="text"
                                    class="form-control" name="id_card" id="id_card" required></div>
                            <div class="mb-2"><label class="form-label">Nombre</label><input type="text"
                                    class="form-control" name="first_name" id="first_name" required></div>
                            <div class="mb-2"><label class="form-label">Apellido</label><input type="text"
                                    class="form-control" name="last_name" id="last_name" required></div>
                            <div class="mb-2"><label class="form-label">Dirección</label><input type="text"
                                    class="form-control" name="address" id="address" required></div>
                            <div class="mb-2"><label class="form-label">Teléfono</label><input type="text"
                                    class="form-control" name="phone" id="phone" required></div>
                            <div class="mb-2"><label class="form-label">Sexo</label><input type="text" class="form-control"
                                    name="gender" id="gender" required></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveStudent()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Course -->
        <div class="modal fade" id="modalCourse" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCourseTitle">Curso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCourse">
                            <input type="hidden" id="course_mode" value="new">
                            <input type="hidden" name="course_id" id="course_id">
                            <div class="mb-2"><label class="form-label">Nombre del Curso</label><input type="text"
                                    class="form-control" name="name" id="name" required></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveCourse()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Enrollment -->
        <div class="modal fade" id="modalEnrollment" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Matrícula</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEnrollment">
                            <!-- Selects will be populated via AJAX -->
                            <div class="mb-3">
                                <label class="form-label">Estudiante</label>
                                <select class="form-select" name="student" id="enr_student" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Curso</label>
                                <select class="form-select" name="course" id="enr_course" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveEnrollment()">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // --- GLOBAL & LOGOUT ---
            function logout() {
                $.post('api.php?route=auth/logout', {}, function () { location.reload(); });
            }

            $(document).ready(function () {
                // Load tables on startup if they exist
                if ($('#tablaStudents').length) loadStudents();
                if ($('#tablaCourses').length) loadCourses();
                if ($('#tablaEnrollments').length) loadEnrollments();

                // Search Listeners
                $('#searchStudent').on('keyup', function () { loadStudents($(this).val()); });
                $('#searchCourse').on('keyup', function () { loadCourses($(this).val()); });
            });

            // --- ESTUDIANTES ---
            function loadStudents(search = '') {
                $.post('api.php?route=students/get', { id_card: search }, function (res) {
                    var data = JSON.parse(res);
                    var html = '';
                    data.forEach(function (row) {
                        html += `<tr>
                        <td>${row.idCard}</td>
                        <td>${row.firstName}</td>
                        <td>${row.lastName}</td>
                        <td>${row.address}</td>
                        <td>${row.phone}</td>
                        <td>${row.gender}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editStudent(${JSON.stringify(row)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteStudent('${row.idCard}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                    });
                    $('#tablaStudents tbody').html(html);
                });
            }
            function newStudent() {
                $('#formStudent')[0].reset();
                $('#student_mode').val('new');
                $('#id_card').prop('readonly', false);
                $('#modalStudentTitle').text('Nuevo Estudiante');
                new bootstrap.Modal('#modalStudent').show();
            }
            function editStudent(row) {
                $('#student_mode').val('edit');
                $('#id_card').val(row.idCard).prop('readonly', true);
                $('#first_name').val(row.firstName);
                $('#last_name').val(row.lastName);
                $('#address').val(row.address);
                $('#phone').val(row.phone);
                $('#gender').val(row.gender);
                $('#modalStudentTitle').text('Edit Student');
                new bootstrap.Modal('#modalStudent').show();
            }
            function saveStudent() {
                var url = $('#student_mode').val() == 'new' ? 'api.php?route=students/create' : 'api.php?route=students/update&id_card=' + $('#id_card').val();
                // Serialize and ensure all fields are sent
                var data = $('#formStudent').serialize();
                $.post(url, data, function (res) {
                    var result = JSON.parse(res);
                    if (result.errorMsg) {
                        alert(result.errorMsg);
                    } else {
                        bootstrap.Modal.getInstance('#modalStudent').hide();
                        loadStudents();
                    }
                });
            }
            function deleteStudent(cedula) {
                if (confirm('¿Está seguro de eliminar este student?')) {
                    $.post('api.php?route=students/delete', { id_card: cedula }, function (res) {
                        var result = JSON.parse(res);
                        if (result.success) loadStudents();
                        else alert(result.errorMsg);
                    });
                }
            }

            // --- CURSOS ---
            function loadCourses(search = '') {
                $.post('api.php?route=courses/get', { name: search }, function (res) {
                    var data = JSON.parse(res);
                    var html = '';
                    data.forEach(function (row) {
                        html += `<tr>
                        <td>${row.id}</td>
                        <td>${row.name}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editCourse(${JSON.stringify(row)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCourse('${row.id}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                    });
                    $('#tablaCourses tbody').html(html);
                });
            }
            function newCourse() {
                $('#formCourse')[0].reset();
                $('#course_mode').val('new');
                $('#modalCourseTitle').text('New Course');
                new bootstrap.Modal('#modalCourse').show();
            }
            function editCourse(row) {
                $('#course_mode').val('edit');
                $('#course_id').val(row.id);
                $('#name').val(row.name);
                $('#modalCourseTitle').text('Edit Course');
                new bootstrap.Modal('#modalCourse').show();
            }
            function saveCourse() {
                var url = $('#course_mode').val() == 'new' ? 'api.php?route=courses/create' : 'api.php?route=courses/update&course_id=' + $('#course_id').val();
                $.post(url, $('#formCourse').serialize(), function (res) {
                    var result = JSON.parse(res);
                    if (result.errorMsg) alert(result.errorMsg);
                    else {
                        bootstrap.Modal.getInstance('#modalCourse').hide();
                        loadCourses();
                    }
                });
            }
            function deleteCourse(id) {
                if (confirm('¿Está seguro de eliminar este course?')) {
                    $.post('api.php?route=courses/delete', { course_id: id }, function (res) {
                        var result = JSON.parse(res);
                        if (result.success) loadCourses();
                        else alert(result.errorMsg);
                    });
                }
            }

            // --- MATRICULAS ---
            function loadEnrollments() {
                $.post('api.php?route=enrollments/get', {}, function (res) {
                    var data = JSON.parse(res);
                    var html = '';
                    data.forEach(function (row) {
                        var nombreComp = row.firstName + ' ' + row.lastName;
                        html += `<tr>
                        <td>${row.id}</td>
                        <td>${nombreComp}</td>
                        <td>${row.courseName}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="deleteEnrollment('${row.id}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                    });
                    $('#tablaEnrollments tbody').html(html);
                });
            }
            function newEnrollment() {
                // Populate selects
                $.post('api.php?route=students/get', {}, function (res) {
                    var ests = JSON.parse(res);
                    var opts = '<option value="">Seleccione...</option>';
                    ests.forEach(e => opts += `<option value="${e.idCard}">${e.firstName} ${e.lastName}</option>`);
                    $('#enr_student').html(opts);
                });
                $.post('api.php?route=courses/get', {}, function (res) {
                    var curs = JSON.parse(res);
                    var opts = '<option value="">Seleccione...</option>';
                    curs.forEach(c => opts += `<option value="${c.id}">${c.name}</option>`);
                    $('#enr_course').html(opts);

                    // Initialize Select2 after data load
                    $('#enr_student').select2({
                        dropdownParent: $('#modalEnrollment'),
                        width: '100%',
                        placeholder: "Seleccione o escriba el nombre..."
                    });
                    $('#enr_course').select2({
                        dropdownParent: $('#modalEnrollment'),
                        width: '100%',
                        placeholder: "Seleccione o escriba el course..."
                    });
                });
                new bootstrap.Modal('#modalEnrollment').show();
            }
            function saveEnrollment() {
                $.post('api.php?route=enrollments/create', $('#formEnrollment').serialize(), function (res) {
                    var result = JSON.parse(res);
                    if (result.errorMsg) alert(result.errorMsg);
                    else {
                        bootstrap.Modal.getInstance('#modalEnrollment').hide();
                        loadEnrollments();
                    }
                });
            }
            function deleteEnrollment(id) {
                if (confirm('¿Está seguro de eliminar esta matrícula?')) {
                    $.post('api.php?route=enrollments/delete', { id: id }, function (res) {
                        var result = JSON.parse(res);
                        if (result.success) loadEnrollments();
                        else alert(result.errorMsg);
                    });
                }
            }

            // --- REPORTS ---
            function genCourseReport() {
                var course_id = $('#rep_course_id').val();
                if (!course_id) return alert('Ingrese ID del course');
                window.open('api.php?route=reports/course&course_id=' + course_id, '_blank');
            }
            function genGenderChart() {
                var course_id = $('#rep_course_id').val();
                if (!course_id) return alert('Ingrese ID del course');
                window.open('api.php?route=reports/gender&course_id=' + course_id, '_blank');
            }
            function genStudentReport() {
                var ced = $('#rep_cedula').val();
                if (!ced) return alert('Ingrese ID Card');
                window.open('api.php?route=reports/student&id_card=' + ced, '_blank');
            }

        </script>
    <?php } ?>
</section>