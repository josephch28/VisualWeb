<?php
session_start();
?>
<section class="pagina">
    <h1>Gestión de Servicios Universitarios</h1>
    <p>Administra estudiantes, cursos y matrículas.</p>

    <link rel="stylesheet" type="text/css" href="jquery/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/color.css">
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.easyui.min.js"></script>

    <?php
    if (!isset($_SESSION['user'])) {
        // --- NOT LOGGED IN: SHOW LOGIN FORM ---
        ?>
        <div id="login-dialog" class="easyui-dialog" title="Login" style="width:400px;padding:30px 60px"
            data-options="closable:false, modal:false, draggable:false, resizable:false, buttons:'#login-buttons'">
            <div style="margin-bottom:20px">
                <input id="login_user" class="easyui-textbox" prompt="Usuario" iconCls="icon-man"
                    style="width:100%;height:34px;padding:12px">
            </div>
            <div style="margin-bottom:20px">
                <input id="login_password" class="easyui-passwordbox" prompt="Contraseña" iconCls="icon-lock"
                    style="width:100%;height:34px;padding:12px">
            </div>
        </div>
        <div id="login-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="submitLogin()"
                style="width:120px">Entrar</a>
            <a href="index.php" class="easyui-linkbutton" iconCls="icon-back" style="width:120px">Salir</a>
        </div>

        <script>
            function submitLogin() {
                var u = $('#login_user').textbox('getValue');
                var p = $('#login_password').passwordbox('getValue');

                $.post('api.php?route=auth/login', { user: u, password: p }, function (result) {
                    var result = eval('(' + result + ')');
                    if (result.success) {
                        location.reload();
                    } else {
                        $.messager.alert({
                            title: 'Error de Acceso',
                            msg: result.errorMsg,
                            icon: 'error',
                            style: { right: '', top: '', bottom: '' } // Centered
                        });
                    }
                });
            }
        </script>
        <?php
    } else {
        // --- LOGGED IN: SHOW CONTENT BASED ON ROLE ---
        $rol = $_SESSION['rol'];
        // Normalize role string just in case/case-insensitive
        $isSecretary = (stripos($rol, 'Secretary') !== false || stripos($rol, 'Secretaria') !== false);
        $isAdmin = (stripos($rol, 'Administrator') !== false || stripos($rol, 'Administrador') !== false);
        ?>
        <div style="text-align: right; margin-bottom: 10px;">
            <strong>Bienvenido, <?php echo $_SESSION['user']; ?> (<?php echo $rol; ?>)</strong>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-back" plain="true"
                onclick="logout()">Cerrar Sesión</a>
        </div>

        <!-- Tabs Container: width 89%, max-width 900px, as requested -->
        <div class="easyui-tabs" style="width:89%; max-width:900px; height:auto; min-height:600px; margin:20px auto;">

            <?php if ($isSecretary || !$isAdmin) { // Default to showing everything if role logic fails or is Secretary ?>
                <!-- Tab Students -->
                <div title="Estudiantes" style="padding:10px; box-sizing: border-box;">
                    <div style="margin-bottom: 20px; margin-top: 20px;">
                        <input id="buscar_cedula" class="easyui-textbox" prompt="Buscar por Cédula"
                            style="width:100%; max-width: 300px;" data-options="
                        inputEvents: $.extend({}, $.fn.textbox.defaults.inputEvents, {
                            keyup: function(e){
                                var t = $(e.data.target);
                                var value = t.textbox('getText');
                                $('#dg').datagrid('load', {
                                    id_card: value
                                });
                            }
                        })
                    ">
                    </div>

                    <!-- Responsive Wrapper -->
                    <div style="width:100%; overflow-x:auto;">
                        <table id="dg" title="Listado de Estudiantes" class="easyui-datagrid"
                            style="width:100%; max-width:750px; height:400px; margin:0 auto;" url="api.php?route=students/get"
                            toolbar="#toolbar" pagination="true" rownumbers="true" singleSelect="true">
                            <thead>
                                <tr>
                                    <th field="idCard" width="100">Cédula</th>
                                    <th field="firstName" width="150">Nombre</th>
                                    <th field="lastName" width="150">Apellido</th>
                                    <th field="address" width="150">Dirección</th>
                                    <th field="phone" width="100">Teléfono</th>
                                    <th field="gender" width="50">Sexo</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div id="toolbar">
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                            onclick="newUser()">Nuevo</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true"
                            onclick="editUser()">Editar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                            onclick="destroyUser()">Eliminar</a>
                    </div>

                    <div id="dlg" class="easyui-dialog" style="width:400px"
                        data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons'">
                        <form id="fm" method="post" novalidate style="margin:0;padding:20px 50px">
                            <h3>Información del Estudiante</h3>
                            <div style="margin-bottom:10px">
                                <input name="id_card" class="easyui-textbox" required="true" label="Cédula:"
                                    style="width:100%">
                            </div>
                            <div style="margin-bottom:10px">
                                <input name="first_name" class="easyui-textbox" required="true" label="Nombre:"
                                    style="width:100%">
                            </div>
                            <div style="margin-bottom:10px">
                                <input name="last_name" class="easyui-textbox" required="true" label="Apellido:"
                                    style="width:100%">
                            </div>
                            <div style="margin-bottom:10px">
                                <input name="address" class="easyui-textbox" required="true" label="Dirección:"
                                    style="width:100%">
                            </div>
                            <div style="margin-bottom:10px">
                                <input name="phone" class="easyui-textbox" required="true" label="Teléfono:" style="width:100%">
                            </div>
                            <div style="margin-bottom:10px">
                                <input name="gender" class="easyui-textbox" required="true" label="Sexo:" style="width:100%">
                            </div>
                        </form>
                    </div>

                    <div id="dlg-buttons">
                        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()"
                            style="width:90px">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
                            onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancelar</a>
                    </div>
                </div>

                <!-- Tab Courses -->
                <div title="Cursos" style="padding:10px; box-sizing: border-box;">
                    <div style="margin-bottom: 20px; margin-top: 20px;">
                        <input id="buscar_curso" class="easyui-textbox" prompt="Buscar por Nombre"
                            style="width:100%; max-width: 300px;" data-options="
                        inputEvents: $.extend({}, $.fn.textbox.defaults.inputEvents, {
                            keyup: function(e){
                                var t = $(e.data.target);
                                var value = t.textbox('getText');
                                $('#dgCourse').datagrid('load', {
                                    name: value
                                });
                            }
                        })
                    ">
                    </div>

                    <div style="width:100%; overflow-x:auto;">
                        <table id="dgCourse" title="Listado de Cursos" class="easyui-datagrid"
                            style="width:100%; max-width:450px; height:400px; margin:0 auto;" url="api.php?route=courses/get"
                            toolbar="#toolbarCourse" pagination="true" rownumbers="true" singleSelect="true">
                            <thead>
                                <tr>
                                    <th field="id" width="80">ID</th>
                                    <th field="name" width="300">Nombre del Curso</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div id="toolbarCourse">
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                            onclick="newCourse()">Nuevo</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true"
                            onclick="editCourse()">Editar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                            onclick="destroyCourse()">Eliminar</a>
                    </div>

                    <div id="dlgCourse" class="easyui-dialog" style="width:400px"
                        data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-course'">
                        <form id="fmCourse" method="post" novalidate style="margin:0;padding:20px 50px">
                            <h3>Información del Curso</h3>
                            <div style="margin-bottom:10px">
                                <input name="name" class="easyui-textbox" required="true" label="Nombre:"
                                    style="width:100%">
                            </div>
                        </form>
                    </div>

                    <div id="dlg-buttons-course">
                        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCourse()"
                            style="width:90px">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
                            onclick="javascript:$('#dlgCourse').dialog('close')" style="width:90px">Cancelar</a>
                    </div>
                </div>

                <!-- Tab Enrollments -->
                <div title="Matrículas" style="padding:10px; box-sizing: border-box;">
                    <div style="width:100%; overflow-x:auto;">
                        <table id="dgEnrollment" title="Listado de Matrículas" class="easyui-datagrid"
                            style="width:100%; max-width:600px; height:400px; margin:0 auto;"
                            url="api.php?route=enrollments/get" toolbar="#toolbarEnrollment" pagination="true" rownumbers="true"
                            singleSelect="true">
                            <thead>
                                <tr>
                                    <th field="id" width="50">ID</th>
                                    <th field="firstName" width="250" formatter="formatNombreCompleto">Estudiante</th>
                                    <th field="courseName" width="250">Curso</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div id="toolbarEnrollment">
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true"
                            onclick="newEnrollment()">Nueva Matrícula</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true"
                            onclick="destroyEnrollment()">Eliminar</a>
                    </div>

                    <div id="dlgEnrollment" class="easyui-dialog" style="width:400px"
                        data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-enrollment'">
                        <form id="fmEnrollment" method="post" novalidate style="margin:0;padding:20px 50px">
                            <h3>Nueva Matrícula</h3>
                            <div style="margin-bottom:10px">
                                <input id="cbStudent" class="easyui-combobox" name="student_id" label="Estudiante:" style="width:100%"
                                    required="true" data-options="
                                    valueField:'idCard',
                                    textField:'lastName',
                                    url:'api.php?route=students/get',
                                    formatter:function(row){
                                        return row.firstName + ' ' + row.lastName;
                                    },
                                    filter: function(q, row){
                                        var opts = $(this).combobox('options');
                                        return row['firstName'].toLowerCase().indexOf(q.toLowerCase()) >= 0 ||
                                            row['lastName'].toLowerCase().indexOf(q.toLowerCase()) >= 0 ||
                                            row['idCard'].indexOf(q) >= 0;
                                    }
                                ">
                            </div>
                            <div style="margin-bottom:10px">
                                <input id="cbCourse" class="easyui-combobox" name="course_id" label="Curso:" style="width:100%"
                                    required="true"
                                    data-options="valueField:'id',textField:'name',url:'api.php?route=courses/get'">
                            </div>
                        </form>
                    </div>

                    <div id="dlg-buttons-enrollment">
                        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveEnrollment()"
                            style="width:90px">Guardar</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel"
                            onclick="javascript:$('#dlgEnrollment').dialog('close')" style="width:90px">Cancelar</a>
                    </div>
                </div>
            <?php } ?>

            <!-- NEW TAB: Reports (Visible for both roles) -->
            <div title="Reportes PDF (FPDF)" style="padding:20px; box-sizing: border-box;">
                <div
                    style="max-width: 600px; margin: 0 auto; background: #fafafa; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                    <h3>Generación de Reportes</h3>
                    <p>Seleccione los parámetros necesarios para generar los reportes.</p>
                    <br>
                    <!-- 1. General Report -->
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <strong>1. Reporte General de Estudiantes</strong><br><br>
                        <a href="api.php?route=reports/general" target="_blank" class="easyui-linkbutton"
                            iconCls="icon-print" style="width:100%">Generar Lista Completa</a>
                    </div>

                    <!-- 2. Students by Course -->
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <strong>2. Reporte de Estudiantes por Curso</strong><br>
                        <div style="margin-top:10px; margin-bottom:10px;">
                            ID del Curso: <input id="rep_course_id" class="easyui-numberbox" style="width:100px;">
                        </div>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print"
                            onclick="genCourseReport()" style="width:200px">Generar por Curso</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-chart-bar"
                            onclick="genGenderChart()" style="width:200px">Ver Gráfico de Género</a>
                    </div>

                    <!-- 3. Student Details -->
                    <div style="margin-bottom: 20px;">
                        <strong>3. Reporte Detallado por Estudiante</strong><br>
                        <div style="margin-top:10px; margin-bottom:10px;">
                            Cédula: <input id="rep_cedula" class="easyui-textbox" style="width:150px;">
                        </div>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-man"
                            onclick="genStudentReport()" style="width:200px">Generar Ficha Estudiante</a>
                    </div>
                </div>
            </div>

        </div>

        <script type="text/javascript">
            var url;

            function logout() {
                $.post('api.php?route=auth/logout', {}, function (result) {
                    location.reload();
                });
            }

            function formatNombreCompleto(val, row) {
                return row.firstName + ' ' + row.lastName;
            }

            // --- Functions for Reports ---
            function genCourseReport() {
                var course_id = $('#rep_course_id').numberbox('getValue');
                if (course_id == '') {
                    $.messager.alert('Error', 'Por favor ingrese el ID del curso', 'warning');
                    return;
                }
                window.open('api.php?route=reports/course&course_id=' + course_id, '_blank');
            }

            function genStudentReport() {
                var ced = $('#rep_cedula').textbox('getValue');
                if (ced == '') {
                    $.messager.alert('Error', 'Por favor ingrese la Cédula del estudiante', 'warning');
                    return;
                }
                window.open('api.php?route=reports/student&id_card=' + ced, '_blank');
            }

            function genGenderChart() {
                var course_id = $('#rep_course_id').numberbox('getValue');
                if (course_id == '') {
                    $.messager.alert('Error', 'Por favor ingrese el ID del curso para el gráfico', 'warning');
                    return;
                }
                window.open('api.php?route=reports/gender&course_id=' + course_id, '_blank');
            }

            // --- Students ---
            function newUser() {
                $('#dlg').dialog('open').dialog('center').dialog('setTitle', 'Nuevo Estudiante');
                $('#fm').form('clear');
                url = 'api.php?route=students/create';
            }
            function editUser() {
                var row = $('#dg').datagrid('getSelected');
                if (row) {
                    $('#dlg').dialog('open').dialog('center').dialog('setTitle', 'Editar Estudiante');
                    $('#fm').form('load', {
                        id_card: row.idCard,
                        first_name: row.firstName,
                        last_name: row.lastName,
                        address: row.address,
                        phone: row.phone,
                        gender: row.gender
                    });
                    url = 'api.php?route=students/update&id_card=' + row.idCard;
                }
            }
            function saveUser() {
                $('#fm').form('submit', {
                    url: url,
                    iframe: false,
                    onSubmit: function () {
                        return $(this).form('validate');
                    },
                    success: function (result) {
                        var result = eval('(' + result + ')');
                        if (result.errorMsg) {
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style: { right: '', top: '', bottom: '' }
                            });
                        } else {
                            $('#dlg').dialog('close');        // close the dialog
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }
                });
            }
            function destroyUser() {
                var row = $('#dg').datagrid('getSelected');
                if (row) {
                    $.messager.confirm('Confirm', '¿Está seguro de eliminar este estudiante?', function (r) {
                        if (r) {
                            $.post('api.php?route=students/delete', { id_card: row.idCard }, function (result) {
                                if (result.success) {
                                    $('#dg').datagrid('reload');    // reload the user data
                                } else {
                                    $.messager.alert({    // show error message
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style: { right: '', top: '', bottom: '' }
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
            }

            // --- Courses ---
            function newCourse() {
                $('#dlgCourse').dialog('open').dialog('center').dialog('setTitle', 'Nuevo Curso');
                $('#fmCourse').form('clear');
                url = 'api.php?route=courses/create';
            }
            function editCourse() {
                var row = $('#dgCourse').datagrid('getSelected');
                if (row) {
                    $('#dlgCourse').dialog('open').dialog('center').dialog('setTitle', 'Editar Curso');
                    $('#fmCourse').form('load', row);
                    url = 'api.php?route=courses/update&course_id=' + row.id;
                }
            }
            function saveCourse() {
                $('#fmCourse').form('submit', {
                    url: url,
                    iframe: false,
                    onSubmit: function () {
                        return $(this).form('validate');
                    },
                    success: function (result) {
                        var result = eval('(' + result + ')');
                        if (result.errorMsg) {
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style: { right: '', top: '', bottom: '' }
                            });
                        } else {
                            $('#dlgCourse').dialog('close');
                            $('#dgCourse').datagrid('reload');
                        }
                    }
                });
            }
            function destroyCourse() {
                var row = $('#dgCourse').datagrid('getSelected');
                if (row) {
                    $.messager.confirm('Confirm', '¿Está seguro de eliminar este curso?', function (r) {
                        if (r) {
                            $.post('api.php?route=courses/delete', { course_id: row.id }, function (result) {
                                if (result.success) {
                                    $('#dgCourse').datagrid('reload');
                                } else {
                                    $.messager.alert({
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style: { right: '', top: '', bottom: '' }
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
            }

            // --- Enrollments ---
            function newEnrollment() {
                $('#dlgEnrollment').dialog('open').dialog('center').dialog('setTitle', 'Nueva Matrícula');
                $('#fmEnrollment').form('clear');

                // Reload ComboBox data
                $('#cbStudent').combobox('reload');
                $('#cbCourse').combobox('reload');

                url = 'api.php?route=enrollments/create';
            }
            function saveEnrollment() {
                $('#fmEnrollment').form('submit', {
                    url: url,
                    iframe: false,
                    onSubmit: function () {
                        return $(this).form('validate');
                    },
                    success: function (result) {
                        var result = eval('(' + result + ')');
                        if (result.errorMsg) {
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style: {
                                    right: '',
                                    top: '',
                                    bottom: ''
                                }
                            });
                        } else {
                            $('#dlgEnrollment').dialog('close');
                            $('#dgEnrollment').datagrid('reload');
                        }
                    }
                });
            }
            function destroyEnrollment() {
                var row = $('#dgEnrollment').datagrid('getSelected');
                if (row) {
                    $.messager.confirm('Confirm', '¿Está seguro de eliminar esta matrícula?', function (r) {
                        if (r) {
                            $.post('api.php?route=enrollments/delete', { id: row.id }, function (result) {
                                if (result.success) {
                                    $('#dgEnrollment').datagrid('reload');
                                } else {
                                    $.messager.alert({
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style: { right: '', top: '', bottom: '' }
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
            }
        </script>

        <br>
        <div style="text-align:center; color:#888;">
            <p>Usuario: <?php echo $_SESSION['user']; ?> | Rol: <?php echo $rol; ?></p>
        </div>
    <?php } ?>
</section>