/*let a=5;
let b=10;
let c=a+b;
console.log("el resultado de la suma es: " + c);
typeof(c);
if(a>b){
    alert("a es mayor que b");
} else {
    alert("b es mayor que a");
}
for(let i=0; i<=10; i++){
    console.log("el valor de i es: " + i);
}
function mensaje(mensaje){
    return mensaje;
}
console.log(mensaje("Cuarto Software"));*/
function sumar(){
    let n1 = document.getElementById("txt1").value;
    let n2 = document.getElementById("txt2").value;
    let resultado = parseFloat(n1) + parseFloat(n2);
    document.getElementById("resultado").innerHTML="El resultado es: "+resultado;
}
function cambiarTexto(){
    document.getElementById("art1").innerHTML="texto cambiado andres gei";
}