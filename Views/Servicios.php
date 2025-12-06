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
    if (!isset($_SESSION['usuario'])) {
        // --- NOT LOGGED IN: SHOW LOGIN FORM ---
    ?>
        <div id="login-dialog" class="easyui-dialog" title="Iniciar Sesión" style="width:400px;padding:30px 60px"
                data-options="closable:false, modal:false, draggable:false, resizable:false, buttons:'#login-buttons'">
            <div style="margin-bottom:20px">
                <input id="login_usuario" class="easyui-textbox" prompt="Usuario" iconCls="icon-man" style="width:100%;height:34px;padding:12px">
            </div>
            <div style="margin-bottom:20px">
                <input id="login_contrasena" class="easyui-passwordbox" prompt="Contraseña" iconCls="icon-lock" style="width:100%;height:34px;padding:12px">
            </div>
        </div>
        <div id="login-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="submitLogin()" style="width:120px">Entrar</a>
            <a href="index.php" class="easyui-linkbutton" iconCls="icon-back" style="width:120px">Salir</a>
        </div>

        <script>
            function submitLogin(){
                var u = $('#login_usuario').textbox('getValue');
                var p = $('#login_contrasena').passwordbox('getValue');
                
                $.post('Models/Login.php', {usuario:u, contrasena:p}, function(result){
                    var result = eval('('+result+')');
                    if (result.success){
                        location.reload();
                    } else {
                        $.messager.alert({
                            title: 'Error de Acceso',
                            msg: result.errorMsg,
                            icon: 'error',
                            style:{ right:'', top:'', bottom:'' } // Centered
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
        $esSecretaria = (stripos($rol, 'Secretaria') !== false);
        $esAdministrador = (stripos($rol, 'Administrador') !== false);
    ?>
        <div style="text-align: right; margin-bottom: 10px;">
            <strong>Bienvenido, <?php echo $_SESSION['usuario']; ?> (<?php echo $rol; ?>)</strong>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-back" plain="true" onclick="logout()">Cerrar Sesión</a>
        </div>

        <!-- Tabs Container: width 89%, max-width 900px, as requested -->
        <div class="easyui-tabs" style="width:89%; max-width:900px; height:auto; min-height:600px; margin:20px auto;">
            
            <?php if ($esSecretaria || !$esAdministrador) { // Default to showing everything if role logic fails or is Secretaria ?>
            <!-- Tab Estudiantes -->
            <div title="Estudiantes" style="padding:10px; box-sizing: border-box;">
                <div style="margin-bottom: 20px; margin-top: 20px;">
                    <input id="estcedula" class="easyui-textbox" prompt="Buscar por Cédula" style="width:100%; max-width: 300px;" data-options="
                        inputEvents: $.extend({}, $.fn.textbox.defaults.inputEvents, {
                            keyup: function(e){
                                var t = $(e.data.target);
                                var value = t.textbox('getText');
                                $('#dg').datagrid('load', {
                                    estcedula: value
                                });
                            }
                        })
                    ">
                </div>

                <!-- Responsive Wrapper -->
                <div style="width:100%; overflow-x:auto;">
                    <table id="dg" title="Listado de Estudiantes" class="easyui-datagrid" style="width:100%; max-width:750px; height:400px; margin:0 auto;"
                            url="Models/AccederEstudiante.php"
                            toolbar="#toolbar" pagination="true"
                            rownumbers="true" singleSelect="true">
                        <thead>
                            <tr>
                                <th field="estcedula" width="100">Cédula</th>
                                <th field="estnombre" width="150">Nombre</th>
                                <th field="estapellido" width="150">Apellido</th>
                                <th field="estdireccion" width="150">Dirección</th>
                                <th field="esttelefono" width="100">Teléfono</th>
                                <th field="estsexo" width="50">Sexo</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div id="toolbar">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newUser()">Nuevo</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editUser()">Editar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyUser()">Eliminar</a>
                </div>
                
                <div id="dlg" class="easyui-dialog" style="width:400px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons'">
                    <form id="fm" method="post" novalidate style="margin:0;padding:20px 50px">
                        <h3>Información del Estudiante</h3>
                        <div style="margin-bottom:10px">
                            <input name="estcedula" class="easyui-textbox" required="true" label="Cédula:" style="width:100%">
                        </div>
                        <div style="margin-bottom:10px">
                            <input name="estnombre" class="easyui-textbox" required="true" label="Nombre:" style="width:100%">
                        </div>
                        <div style="margin-bottom:10px">
                            <input name="estapellido" class="easyui-textbox" required="true" label="Apellido:" style="width:100%">
                        </div>
                        <div style="margin-bottom:10px">
                            <input name="estdireccion" class="easyui-textbox" required="true" label="Dirección:" style="width:100%">
                        </div>
                        <div style="margin-bottom:10px">
                            <input name="esttelefono" class="easyui-textbox" required="true" label="Teléfono:" style="width:100%">
                        </div>
                        <div style="margin-bottom:10px">
                            <input name="estsexo" class="easyui-textbox" required="true" label="Sexo:" style="width:100%">
                        </div>
                    </form>
                </div>
                
                <div id="dlg-buttons">
                    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveUser()" style="width:90px">Guardar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancelar</a>
                </div>
            </div>

            <!-- Tab Cursos -->
            <div title="Cursos" style="padding:10px; box-sizing: border-box;">
                <div style="margin-bottom: 20px; margin-top: 20px;">
                    <input id="curnombre" class="easyui-textbox" prompt="Buscar por Nombre" style="width:100%; max-width: 300px;" data-options="
                        inputEvents: $.extend({}, $.fn.textbox.defaults.inputEvents, {
                            keyup: function(e){
                                var t = $(e.data.target);
                                var value = t.textbox('getText');
                                $('#dgCurso').datagrid('load', {
                                    curnombre: value
                                });
                            }
                        })
                    ">
                </div>

                <div style="width:100%; overflow-x:auto;">
                    <table id="dgCurso" title="Listado de Cursos" class="easyui-datagrid" style="width:100%; max-width:450px; height:400px; margin:0 auto;"
                            url="Models/AccederCurso.php"
                            toolbar="#toolbarCurso" pagination="true"
                            rownumbers="true" singleSelect="true">
                        <thead>
                            <tr>
                                <th field="curid" width="80">ID</th>
                                <th field="curnombre" width="300">Nombre del Curso</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div id="toolbarCurso">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newCurso()">Nuevo</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editCurso()">Editar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyCurso()">Eliminar</a>
                </div>
                
                <div id="dlgCurso" class="easyui-dialog" style="width:400px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-curso'">
                    <form id="fmCurso" method="post" novalidate style="margin:0;padding:20px 50px">
                        <h3>Información del Curso</h3>
                        <div style="margin-bottom:10px">
                            <input name="curnombre" class="easyui-textbox" required="true" label="Nombre:" style="width:100%">
                        </div>
                    </form>
                </div>
                
                <div id="dlg-buttons-curso">
                    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveCurso()" style="width:90px">Guardar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgCurso').dialog('close')" style="width:90px">Cancelar</a>
                </div>
            </div>

            <!-- Tab Matrículas -->
            <div title="Matrículas" style="padding:10px; box-sizing: border-box;">
                <div style="width:100%; overflow-x:auto;">
                    <table id="dgMatricula" title="Listado de Matrículas" class="easyui-datagrid" style="width:100%; max-width:600px; height:400px; margin:0 auto;"
                            url="Models/AccederMatricula.php"
                            toolbar="#toolbarMatricula" pagination="true"
                            rownumbers="true" singleSelect="true">
                        <thead>
                            <tr>
                                <th field="id" width="50">ID</th>
                                <th field="estnombre" width="250" formatter="formatNombreCompleto">Estudiante</th>
                                <th field="curnombre" width="250">Curso</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                
                <div id="toolbarMatricula">
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newMatricula()">Nueva Matrícula</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyMatricula()">Eliminar</a>
                </div>
                
                <div id="dlgMatricula" class="easyui-dialog" style="width:400px" data-options="closed:true,modal:true,border:'thin',buttons:'#dlg-buttons-matricula'">
                    <form id="fmMatricula" method="post" novalidate style="margin:0;padding:20px 50px">
                        <h3>Nueva Matrícula</h3>
                        <div style="margin-bottom:10px">
                            <input id="cbEstudiante" class="easyui-combobox" name="estudiante" label="Estudiante:" style="width:100%" required="true"
                                data-options="
                                    valueField:'estcedula',
                                    textField:'estapellido',
                                    url:'Models/AccederEstudiante.php',
                                    formatter:function(row){
                                        return row.estnombre + ' ' + row.estapellido;
                                    },
                                    filter: function(q, row){
                                        var opts = $(this).combobox('options');
                                        return row['estnombre'].toLowerCase().indexOf(q.toLowerCase()) >= 0 ||
                                            row['estapellido'].toLowerCase().indexOf(q.toLowerCase()) >= 0 ||
                                            row['estcedula'].indexOf(q) >= 0;
                                    }
                                ">
                        </div>
                        <div style="margin-bottom:10px">
                            <input id="cbCurso" class="easyui-combobox" name="curso" label="Curso:" style="width:100%" required="true"
                                data-options="valueField:'curid',textField:'curnombre',url:'Models/AccederCurso.php'">
                        </div>
                    </form>
                </div>
                
                <div id="dlg-buttons-matricula">
                    <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveMatricula()" style="width:90px">Guardar</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlgMatricula').dialog('close')" style="width:90px">Cancelar</a>
                </div>
            </div>
            <?php } ?>

            <!-- NEW TAB: Reportes (Visible for both roles) -->
            <div title="Reportes PDF (FPDF)" style="padding:20px; box-sizing: border-box;">
                <div style="max-width: 600px; margin: 0 auto; background: #fafafa; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
                    <h3>Generación de Reportes</h3>
                    <p>Seleccione los parámetros necesarios para generar los reportes.</p>
                    <br>
                    <!-- 1. General Report -->
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <strong>1. Reporte General de Estudiantes</strong><br><br>
                        <a href="Reportes/reporte.php" target="_blank" class="easyui-linkbutton" iconCls="icon-print" style="width:200px">Generar Lista Completa</a>
                    </div>

                    <!-- 2. Students by Course -->
                    <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <strong>2. Reporte de Estudiantes por Curso</strong><br>
                        <div style="margin-top:10px; margin-bottom:10px;">
                            Curso ID: <input id="rep_curid" class="easyui-numberbox" style="width:100px;">
                        </div>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" onclick="genReporteCurso()" style="width:200px">Generar por Curso</a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-chart-bar" onclick="genGraficoGenero()" style="width:200px">Ver Gráfico Género</a>
                    </div>

                    <!-- 3. Student Details -->
                    <div style="margin-bottom: 20px;">
                        <strong>3. Reporte Detallado por Estudiante</strong><br>
                        <div style="margin-top:10px; margin-bottom:10px;">
                            Cédula: <input id="rep_cedula" class="easyui-textbox" style="width:150px;">
                        </div>
                        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-man" onclick="genReporteEstudiante()" style="width:200px">Generar Ficha Estudiante</a>
                    </div>
                </div>
            </div>

        </div>

        <script type="text/javascript">
            var url;
            
            function logout(){
                $.post('Models/Logout.php', {}, function(result){
                    location.reload();
                });
            }

            function formatNombreCompleto(val, row){
                return row.estnombre + ' ' + row.estapellido;
            }

            // --- Functions for Reports ---
            function genReporteCurso(){
                var curid = $('#rep_curid').numberbox('getValue');
                if(curid == ''){
                    $.messager.alert('Error','Por favor ingrese el ID del curso','warning');
                    return;
                }
                window.open('Reportes/reporte_curso_estudiantes.php?curid=' + curid, '_blank');
            }

            function genReporteEstudiante(){
                var cedula = $('#rep_cedula').textbox('getValue');
                if(cedula == ''){
                    $.messager.alert('Error','Por favor ingrese la Cédula del estudiante','warning');
                    return;
                }
                window.open('Reportes/reporte_estudiante_detalle.php?estcedula=' + cedula, '_blank');
            }

            function genGraficoGenero(){
                var curid = $('#rep_curid').numberbox('getValue');
                if(curid == ''){
                    $.messager.alert('Error','Por favor ingrese el ID del curso para el gráfico','warning');
                    return;
                }
                window.open('Reportes/reporte_genero_grafico.php?curid=' + curid, '_blank');
            }

            // --- Estudiantes ---
            function newUser(){
                $('#dlg').dialog('open').dialog('center').dialog('setTitle','Nuevo Estudiante');
                $('#fm').form('clear');
                url = 'Models/Guardar.php';
            }
            function editUser(){
                var row = $('#dg').datagrid('getSelected');
                if (row){
                    $('#dlg').dialog('open').dialog('center').dialog('setTitle','Editar Estudiante');
                    $('#fm').form('load',row);
                    url = 'Models/Actualizar.php?estcedula='+row.estcedula;
                }
            }
            function saveUser(){
                $('#fm').form('submit',{
                    url: url,
                    iframe: false,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        var result = eval('('+result+')');
                        if (result.errorMsg){
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style:{ right:'', top:'', bottom:'' }
                            });
                        } else {
                            $('#dlg').dialog('close');        // close the dialog
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }
                });
            }
            function destroyUser(){
                var row = $('#dg').datagrid('getSelected');
                if (row){
                    $.messager.confirm('Confirm','Está seguro de eliminar este estudiante?',function(r){
                        if (r){
                            $.post('Models/Eliminar.php',{estcedula:row.estcedula},function(result){
                                if (result.success){
                                    $('#dg').datagrid('reload');    // reload the user data
                                } else {
                                    $.messager.alert({    // show error message
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style:{ right:'', top:'', bottom:'' }
                                    });
                                }
                            },'json');
                        }
                    });
                }
            }

            // --- Cursos ---
            function newCurso(){
                $('#dlgCurso').dialog('open').dialog('center').dialog('setTitle','Nuevo Curso');
                $('#fmCurso').form('clear');
                url = 'Models/GuardarCurso.php';
            }
            function editCurso(){
                var row = $('#dgCurso').datagrid('getSelected');
                if (row){
                    $('#dlgCurso').dialog('open').dialog('center').dialog('setTitle','Editar Curso');
                    $('#fmCurso').form('load',row);
                    url = 'Models/ActualizarCurso.php?curid='+row.curid;
                }
            }
            function saveCurso(){
                $('#fmCurso').form('submit',{
                    url: url,
                    iframe: false,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        var result = eval('('+result+')');
                        if (result.errorMsg){
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style:{ right:'', top:'', bottom:'' }
                            });
                        } else {
                            $('#dlgCurso').dialog('close');
                            $('#dgCurso').datagrid('reload');
                        }
                    }
                });
            }
            function destroyCurso(){
                var row = $('#dgCurso').datagrid('getSelected');
                if (row){
                    $.messager.confirm('Confirm','Está seguro de eliminar este curso?',function(r){
                        if (r){
                            $.post('Models/EliminarCurso.php',{curid:row.curid},function(result){
                                if (result.success){
                                    $('#dgCurso').datagrid('reload');
                                } else {
                                    $.messager.alert({
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style:{ right:'', top:'', bottom:'' }
                                    });
                                }
                            },'json');
                        }
                    });
                }
            }

            // --- Matrículas ---
            function newMatricula(){
                $('#dlgMatricula').dialog('open').dialog('center').dialog('setTitle','Nueva Matrícula');
                $('#fmMatricula').form('clear');
                
                // Reload ComboBox data
                $('#cbEstudiante').combobox('reload');
                $('#cbCurso').combobox('reload');

                url = 'Models/GuardarMatricula.php';
            }
            function saveMatricula(){
                $('#fmMatricula').form('submit',{
                    url: url,
                    iframe: false,
                    onSubmit: function(){
                        return $(this).form('validate');
                    },
                    success: function(result){
                        var result = eval('('+result+')');
                        if (result.errorMsg){
                            $.messager.alert({
                                title: 'Error',
                                msg: result.errorMsg,
                                icon: 'error',
                                style:{
                                    right:'',
                                    top:'',
                                    bottom:''
                                }
                            });
                        } else {
                            $('#dlgMatricula').dialog('close');
                            $('#dgMatricula').datagrid('reload');
                        }
                    }
                });
            }
            function destroyMatricula(){
                var row = $('#dgMatricula').datagrid('getSelected');
                if (row){
                    $.messager.confirm('Confirm','Está seguro de eliminar esta matrícula?',function(r){
                        if (r){
                            $.post('Models/EliminarMatricula.php',{id:row.id},function(result){
                                if (result.success){
                                    $('#dgMatricula').datagrid('reload');
                                } else {
                                    $.messager.alert({
                                        title: 'Error',
                                        msg: result.errorMsg,
                                        icon: 'error',
                                        style:{ right:'', top:'', bottom:'' }
                                    });
                                }
                            },'json');
                        }
                    });
                }
            }
        </script>
        
        <br>
        <div style="text-align:center; color:#888;">
            <p>Usuario: <?php echo $_SESSION['usuario']; ?> | Rol: <?php echo $rol; ?></p>
        </div>
    <?php } ?>
</section>
