<?php
    class EnlacesPaginas{
        public static function enlacesPaginasModel($enlacesModel){
            if($enlacesModel == "Inicio" || 
                $enlacesModel == "Nosotros" ||
                $enlacesModel == "Servicios" ||
                $enlacesModel == "Contacto"){
                $module = "Views/" . $enlacesModel . ".php";
        }   else{
                $module = "Views/Inicio.php";
            }
            return $module;
    }
}
?>