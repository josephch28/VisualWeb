class suma{
    constructor(a, b){
        this.a = a;
        this.b = b;
    }
    sumarNumeros(){
        return this.a + this.b;
    }
}

function sumar(){
    let n1 = document.getElementById("txt1").value;
    let n2 = document.getElementById("txt2").value;
    var objeto = new suma(parseFloat(n1), parseFloat(n2));
    document.getElementById("resultado").innerHTML="El resultado es: "+objeto.sumarNumeros();
}
