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
            <h1>Gestión de Servicios (Bootstrap)</h1>
            <p>Administración universitaria con interfaz moderna.</p>
        </div>
        <div class="col-md-3 text-end" id="userInfo">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span class="badge bg-secondary"><?php echo $_SESSION['usuario']; ?> (<?php echo $_SESSION['rol']; ?>)</span>
                <button onclick="logout()" class="btn btn-outline-danger btn-sm ms-2">Cerrar Sesión</button>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if (!isset($_SESSION['usuario'])) {
        // --- NOT LOGGED IN: SHOW LOGIN MODAL ---
    ?>
        <!-- Static Backdrop Modal for Login -->
        <div class="modal fade show" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Iniciar Sesión</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="loginUser" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="loginUser" placeholder="Ingrese usuario">
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
            function submitLogin(){
                var u = $('#loginUser').val();
                var p = $('#loginPass').val();
                $.post('Models/Login.php', {usuario:u, contrasena:p}, function(res){
                    var data = JSON.parse(res);
                    if(data.success){
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
        $esSecretaria = (stripos($rol, 'Secretaria') !== false);
        $esAdministrador = (stripos($rol, 'Administrador') !== false);
    ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-3" id="mainTabs" role="tablist">
        <?php if ($esSecretaria || !$esAdministrador): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="estudiantes-tab" data-bs-toggle="tab" data-bs-target="#estudiantes" type="button" role="tab">Estudiantes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab">Cursos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="matriculas-tab" data-bs-toggle="tab" data-bs-target="#matriculas" type="button" role="tab">Matrículas</button>
            </li>
        <?php endif; ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo ($esAdministrador && !$esSecretaria) ? 'active' : ''; ?>" id="reportes-tab" data-bs-toggle="tab" data-bs-target="#reportes" type="button" role="tab">Reportes PDF</button>
        </li>
    </ul>

    <div class="tab-content" id="mainTabsContent">
        
        <?php if ($esSecretaria || !$esAdministrador): ?>
        <!-- TAB ESTUDIANTES -->
        <div class="tab-pane fade show active" id="estudiantes" role="tabpanel">
            <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" id="busquedaEstudiante" class="form-control" placeholder="Buscar por Cédula...">
                </div>
                <div class="col-md-8 text-end">
                    <button class="btn btn-primary" onclick="nuevoEstudiante()"><i class="bi bi-person-plus"></i> Nuevo Estudiante</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaEstudiantes">
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
        <div class="tab-pane fade" id="cursos" role="tabpanel">
             <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" id="busquedaCurso" class="form-control" placeholder="Buscar por Nombre...">
                </div>
                <div class="col-md-8 text-end">
                    <button class="btn btn-primary" onclick="nuevoCurso()"><i class="bi bi-plus-circle"></i> Nuevo Curso</button>
                </div>
            </div>
             <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaCursos">
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
        <div class="tab-pane fade" id="matriculas" role="tabpanel">
            <div class="row mb-3 text-end">
                <div class="col-12">
                    <button class="btn btn-success" onclick="nuevaMatricula()"><i class="bi bi-card-checklist"></i> Nueva Matrícula</button>
                </div>
            </div>
             <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaMatriculas">
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
        <div class="tab-pane fade <?php echo ($esAdministrador && !$esSecretaria) ? 'show active' : ''; ?>" id="reportes" role="tabpanel">
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-header bg-primary text-white">
                     Generación de Reportes
                </div>
                <div class="card-body">
                    <p class="card-text">Seleccione las opciones para descargar sus reportes en PDF.</p>
                    
                    <div class="d-grid gap-2 mb-4 pb-3 border-bottom">
                         <label class="fw-bold">1. Reporte General</label>
                         <a href="Reportes/reporte.php" target="_blank" class="btn btn-outline-primary"><i class="bi bi-printer"></i> Generar Lista Completa</a>
                    </div>
                    
                    <div class="mb-4 pb-3 border-bottom">
                         <label class="fw-bold mb-2">2. Por Curso</label>
                         <div class="input-group mb-2">
                             <span class="input-group-text">ID Curso</span>
                             <input type="number" class="form-control" id="rep_curid">
                         </div>
                         <div class="d-grid gap-2">
                            <button onclick="genReporteCurso()" class="btn btn-outline-dark"><i class="bi bi-people"></i> Lista de Estudiantes</button>
                            <button onclick="genGraficoGenero()" class="btn btn-outline-info"><i class="bi bi-bar-chart"></i> Gráfico de Género</button>
                         </div>
                    </div>

                    <div class="mb-2">
                         <label class="fw-bold mb-2">3. Por Estudiante</label>
                         <div class="input-group mb-2">
                             <span class="input-group-text">Cédula</span>
                             <input type="text" class="form-control" id="rep_cedula">
                         </div>
                         <div class="d-grid gap-2">
                            <button onclick="genReporteEstudiante()" class="btn btn-outline-success"><i class="bi bi-person-badge"></i> Ficha Detallada</button>
                         </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODALS FOR CRUD -->
    
    <!-- Modal Estudiante -->
    <div class="modal fade" id="modalEstudiante" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEstudianteTitle">Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEstudiante">
                        <input type="hidden" id="est_mode" value="new">
                        <div class="mb-2"><label class="form-label">Cédula</label><input type="text" class="form-control" name="estcedula" id="estcedula" required></div>
                        <div class="mb-2"><label class="form-label">Nombre</label><input type="text" class="form-control" name="estnombre" id="estnombre" required></div>
                        <div class="mb-2"><label class="form-label">Apellido</label><input type="text" class="form-control" name="estapellido" id="estapellido" required></div>
                        <div class="mb-2"><label class="form-label">Dirección</label><input type="text" class="form-control" name="estdireccion" id="estdireccion" required></div>
                        <div class="mb-2"><label class="form-label">Teléfono</label><input type="text" class="form-control" name="esttelefono" id="esttelefono" required></div>
                        <div class="mb-2"><label class="form-label">Sexo</label><input type="text" class="form-control" name="estsexo" id="estsexo" required></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEstudiante()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Curso -->
    <div class="modal fade" id="modalCurso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCursoTitle">Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                     <form id="formCurso">
                        <input type="hidden" id="cur_mode" value="new">
                        <input type="hidden" name="curid" id="curid">
                        <div class="mb-2"><label class="form-label">Nombre del Curso</label><input type="text" class="form-control" name="curnombre" id="curnombre" required></div>
                     </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCurso()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Matricula -->
    <div class="modal fade" id="modalMatricula" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Matrícula</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formMatricula">
                        <!-- Selects will be populated via AJAX -->
                        <div class="mb-3">
                            <label class="form-label">Estudiante</label>
                            <select class="form-select" name="estudiante" id="mat_estudiante" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Curso</label>
                            <select class="form-select" name="curso" id="mat_curso" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarMatricula()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- GLOBAL & LOGOUT ---
        function logout(){
            $.post('Models/Logout.php', {}, function(){ location.reload(); });
        }

        $(document).ready(function(){
            // Load tables on startup if they exist
            if($('#tablaEstudiantes').length) loadEstudiantes();
            if($('#tablaCursos').length) loadCursos();
            if($('#tablaMatriculas').length) loadMatriculas();

            // Search Listeners
            $('#busquedaEstudiante').on('keyup', function(){ loadEstudiantes($(this).val()); });
            $('#busquedaCurso').on('keyup', function(){ loadCursos($(this).val()); });
        });

        // --- ESTUDIANTES ---
        function loadEstudiantes(search=''){
            $.post('Models/AccederEstudiante.php', {estcedula: search}, function(res){
                var data = JSON.parse(res);
                var html = '';
                data.forEach(function(row){
                    html += `<tr>
                        <td>${row.estcedula}</td>
                        <td>${row.estnombre}</td>
                        <td>${row.estapellido}</td>
                        <td>${row.estdireccion}</td>
                        <td>${row.esttelefono}</td>
                        <td>${row.estsexo}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editarEstudiante(${JSON.stringify(row)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarEstudiante('${row.estcedula}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
                $('#tablaEstudiantes tbody').html(html);
            });
        }
        function nuevoEstudiante(){
            $('#formEstudiante')[0].reset();
            $('#est_mode').val('new');
            $('#estcedula').prop('readonly', false);
            $('#modalEstudianteTitle').text('Nuevo Estudiante');
            new bootstrap.Modal('#modalEstudiante').show();
        }
        function editarEstudiante(row){
            $('#est_mode').val('edit');
            $('#estcedula').val(row.estcedula).prop('readonly', true);
            $('#estnombre').val(row.estnombre);
            $('#estapellido').val(row.estapellido);
            $('#estdireccion').val(row.estdireccion);
            $('#esttelefono').val(row.esttelefono);
            $('#estsexo').val(row.estsexo);
             $('#modalEstudianteTitle').text('Editar Estudiante');
            new bootstrap.Modal('#modalEstudiante').show();
        }
        function guardarEstudiante(){
            var url = $('#est_mode').val() == 'new' ? 'Models/Guardar.php' : 'Models/Actualizar.php?estcedula='+$('#estcedula').val();
            // Serialize and ensure all fields are sent
            var data = $('#formEstudiante').serialize();
            $.post(url, data, function(res){
                var result = JSON.parse(res);
                if(result.errorMsg){
                    alert(result.errorMsg);
                } else {
                    bootstrap.Modal.getInstance('#modalEstudiante').hide();
                    loadEstudiantes();
                }
            });
        }
        function eliminarEstudiante(cedula){
            if(confirm('¿Está seguro de eliminar este estudiante?')){
                $.post('Models/Eliminar.php', {estcedula: cedula}, function(res){
                    var result = JSON.parse(res);
                    if(result.success) loadEstudiantes();
                    else alert(result.errorMsg);
                });
            }
        }

        // --- CURSOS ---
        function loadCursos(search=''){
             $.post('Models/AccederCurso.php', {curnombre: search}, function(res){
                var data = JSON.parse(res);
                var html = '';
                data.forEach(function(row){
                    html += `<tr>
                        <td>${row.curid}</td>
                        <td>${row.curnombre}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick='editarCurso(${JSON.stringify(row)})'><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarCurso('${row.curid}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
                $('#tablaCursos tbody').html(html);
            });
        }
        function nuevoCurso(){
            $('#formCurso')[0].reset();
            $('#cur_mode').val('new');
             $('#modalCursoTitle').text('Nuevo Curso');
            new bootstrap.Modal('#modalCurso').show();
        }
        function editarCurso(row){
            $('#cur_mode').val('edit');
            $('#curid').val(row.curid);
            $('#curnombre').val(row.curnombre);
            $('#modalCursoTitle').text('Editar Curso');
            new bootstrap.Modal('#modalCurso').show();
        }
        function guardarCurso(){
            var url = $('#cur_mode').val() == 'new' ? 'Models/GuardarCurso.php' : 'Models/ActualizarCurso.php?curid='+$('#curid').val();
            $.post(url, $('#formCurso').serialize(), function(res){
                 var result = JSON.parse(res);
                 if(result.errorMsg) alert(result.errorMsg);
                 else {
                     bootstrap.Modal.getInstance('#modalCurso').hide();
                     loadCursos();
                 }
            });
        }
        function eliminarCurso(id){
             if(confirm('¿Está seguro de eliminar este curso?')){
                $.post('Models/EliminarCurso.php', {curid: id}, function(res){
                    var result = JSON.parse(res);
                    if(result.success) loadCursos();
                    else alert(result.errorMsg);
                });
            }
        }

        // --- MATRICULAS ---
        function loadMatriculas(){
             $.post('Models/AccederMatricula.php', {}, function(res){
                var data = JSON.parse(res);
                var html = '';
                data.forEach(function(row){
                    var nombreComp = row.estnombre + ' ' + row.estapellido;
                    html += `<tr>
                        <td>${row.id}</td>
                        <td>${nombreComp}</td>
                        <td>${row.curnombre}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="eliminarMatricula('${row.id}')"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>`;
                });
                $('#tablaMatriculas tbody').html(html);
            });
        }
        function nuevaMatricula(){
            // Populate selects
            $.post('Models/AccederEstudiante.php', {}, function(res){
                var ests = JSON.parse(res);
                var opts = '<option value="">Seleccione...</option>';
                ests.forEach(e => opts += `<option value="${e.estcedula}">${e.estnombre} ${e.estapellido}</option>`);
                $('#mat_estudiante').html(opts);
            });
            $.post('Models/AccederCurso.php', {}, function(res){
                var curs = JSON.parse(res);
                var opts = '<option value="">Seleccione...</option>';
                curs.forEach(c => opts += `<option value="${c.curid}">${c.curnombre}</option>`);
                $('#mat_curso').html(opts);
                
                // Initialize Select2 after data load
                $('#mat_estudiante').select2({
                    dropdownParent: $('#modalMatricula'),
                    width: '100%',
                    placeholder: "Seleccione o escriba el nombre..."
                });
                $('#mat_curso').select2({
                    dropdownParent: $('#modalMatricula'),
                    width: '100%',
                    placeholder: "Seleccione o escriba el curso..."
                });
            });
            new bootstrap.Modal('#modalMatricula').show();
        }
        function guardarMatricula(){
             $.post('Models/GuardarMatricula.php', $('#formMatricula').serialize(), function(res){
                 var result = JSON.parse(res);
                 if(result.errorMsg) alert(result.errorMsg);
                 else {
                     bootstrap.Modal.getInstance('#modalMatricula').hide();
                     loadMatriculas();
                 }
            });
        }
        function eliminarMatricula(id){
             if(confirm('¿Está seguro de eliminar esta matrícula?')){
                $.post('Models/EliminarMatricula.php', {id: id}, function(res){
                     var result = JSON.parse(res);
                    if(result.success) loadMatriculas();
                    else alert(result.errorMsg);
                });
            }
        }

        // --- REPORTS ---
        function genReporteCurso(){
            var curid = $('#rep_curid').val();
            if(!curid) return alert('Ingrese ID del curso');
            window.open('Reportes/reporte_curso_estudiantes.php?curid=' + curid, '_blank');
        }
        function genGraficoGenero(){
            var curid = $('#rep_curid').val();
            if(!curid) return alert('Ingrese ID del curso');
            window.open('Reportes/reporte_genero_grafico.php?curid=' + curid, '_blank');
        }
         function genReporteEstudiante(){
            var ced = $('#rep_cedula').val();
            if(!ced) return alert('Ingrese Cédula');
            window.open('Reportes/reporte_estudiante_detalle.php?estcedula=' + ced, '_blank');
        }

    </script>
    <?php } ?>
</section>
