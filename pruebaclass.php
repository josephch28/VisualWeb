<?php   
    class Automovil{
        public $placa;
        public $marca;
        public $modelo;
        
        public function __construct($placa, $marca, $modelo){
            $this->placa = $placa;
            $this->marca = $marca;
            $this->modelo = $modelo;
        }
        public function imprimir(){
            echo "Placa: " . $this->placa . "<br>";
            echo "Marca: " . $this->marca . "<br>";
            echo "Modelo: " . $this->modelo . "<br>";
        } 

    }
    $auto1 = new Automovil("TBJ3333", "Chevrolet", "Camaro");
    $auto1->imprimir();
?>