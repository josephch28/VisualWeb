<?php
    require_once "Models/model.php";
    class EnlacesPaginaController{
        public function plantilla(){
            include "Views/Inicio.php";
        }
        public function enlacesPaginasController(){
            if(isset($_GET["accion"])){
                $enlacescontroller = $_GET["accion"];
            }else{
                $enlacescontroller = "Inicio";
            }
           // $var = new EnlacesPaginas();
           // $respuesta = $var->enlacesPaginasModel($enlacescontroller);
            $respuesta = EnlacesPaginas::enlacesPaginasModel($enlacescontroller);
            include $respuesta;
        }

    }

?>