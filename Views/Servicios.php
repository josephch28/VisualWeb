<section class="pagina">
    <h1>Gestión de Estudiantes</h1>
    <p>Administra la información de los estudiantes de la universidad. Puedes buscar, agregar, editar o eliminar registros.</p> 
    
    <link rel="stylesheet" type="text/css" href="jquery/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="jquery/themes/color.css">
    <link rel="stylesheet" type="text/css" href="jquery/demo/demo.css">
    <script type="text/javascript" src="jquery/jquery.min.js"></script>
    <script type="text/javascript" src="jquery/jquery.easyui.min.js"></script>

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

    <table id="dg" title="Listado de Estudiantes" class="easyui-datagrid" style="width:100%; height:400px"
            url="Models/AccederEstudiante.php"
            toolbar="#toolbar" pagination="true"
            rownumbers="true" fitColumns="true" singleSelect="true">
        <thead>
            <tr>
                <th field="estcedula" width="50">Cédula</th>
                <th field="estnombre" width="50">Nombre</th>
                <th field="estapellido" width="50">Apellido</th>
                <th field="estdireccion" width="50">Dirección</th>
                <th field="esttelefono" width="50">Teléfono</th>
                <th field="estsexo" width="50">Sexo</th>
            </tr>
        </thead>
    </table>
    
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

    <script type="text/javascript">
        var url;
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
                        $.messager.show({
                            title: 'Error',
                            msg: result.errorMsg
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
                                $.messager.show({    // show error message
                                    title: 'Error',
                                    msg: result.errorMsg
                                });
                            }
                        },'json');
                    }
                });
            }
        }
    </script>
    <br>
    <div>
        <a href="reporte.php" target="_blank" class="easyui-linkbutton" iconCls="icon-print">Generar Reporte PDF (FPDF)</a>
        <a href="Reportes/ConJasper/ReporteJasper.php" target="_blank" class="easyui-linkbutton" iconCls="icon-print">Generar Reporte PDF (Jasper)</a>
    </div>
</section>
