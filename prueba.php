<?php
    $numero = 3;
    echo "El número es: " . $numero;
    print_r( $numero );
    var_dump( $numero );
    echo "<br>";
    $cadena = "Hola mundo";
    echo "La cadena es: " . $cadena;    
    print_r( $cadena );
    var_dump( $cadena );
    echo "<br>";

    $bole = true;
    echo "El valor booleano es: " . $bole;
    print_r( $bole );
    var_dump( $bole );
    echo "<br>";

    $vector = array("rojo", "azul");
    echo $vector[0];
    print_r( $vector );
    var_dump( $vector );
    echo "<br>";

    //arreglo de propiedades
    $colores = array("color1" => "amarillo", "color2" => "rojo");
    echo $colores["color1"];
    echo "<br>";

    //objetos
    $objeto = (object) [
        "color1" => "amarillo", 
        "color2" => "rojo"
    ];
    echo $objeto->color2;
    var_dump( $objeto );
    echo "<br>";

    function saludar() {
        echo "Hola ";
    }
    saludar();
    echo "<br>";
    
    function saludarNombre($nombre) {
        echo "Hola " . $nombre;
    }
    saludarNombre("Ana");
    echo "<br>";

    $automovil1 = (object) [
        "Placa" => "TBJ1111",
        "marca" => "Ford",
        "modelo" => "F150"
    ];
    $automovil2 = (object) [
        "Placa" => "TBJ2222",
        "marca" => "Toyota",
        "modelo" => "RAV4"
    ];

    function mostrarAutomovil($auto) {
        echo "Placa: " . $auto->Placa . ", Marca: " . $auto->marca . ", Modelo: " . $auto->modelo;
    }

    mostrarAutomovil($automovil1);
    echo "<br>";
    mostrarAutomovil($automovil2);
    echo "<br>";

    $num1 = 8;
    $num2 = 8;
    if ($num1 > $num2){
        echo $num1 . " es mayor que " . $num2;
    } else if ($num1 < $num2){
        echo $num2 . " es mayor que " . $num1;
    } else {
        echo $num1 . " es igual que " . $num2;
    }

    for($i=0;$i<=5;$i++){
        echo "<br>". $i;
    }
?>