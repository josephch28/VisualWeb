<?php
    class PageLinksController{
        public function template(){
            include "Views/Inicio.php";
        }
        
        public function pageLinksController(){
            if(isset($_GET["accion"])){
                $linksController = $_GET["accion"];
            }else{
                $linksController = "Inicio";
            }
            
            $response = $this->pageLinksModel($linksController);
            include $response;
        }

        private function pageLinksModel($enlacesModel){
            $allowedViews = ["Inicio", "Nosotros", "Servicios", "Contacto"];
            
            if(in_array($enlacesModel, $allowedViews)){
                $module = "Views/" . $enlacesModel . ".php";
            } else {
                $module = "Views/Inicio.php";
            }
            return $module;
        }
    }
?>