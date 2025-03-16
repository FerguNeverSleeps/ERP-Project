//console.log("Funciones ag_integrantes");

function actualizar(valor1)
{
    var edad = calcular_edad(valor1)
    var edad = edad.toString()

    $("#edad").html(edad + (edad=='1' ? " A&#241;o" : " A&#241;os"))
}

function antiguedad(fecha)
{
//    alert(fecha);
    fecha = fecha.replace("/", "-");
    par   = fecha.split("-");
    fecha = par[2]+'-'+par[1]+'-'+par[0] + ' 00:00:00';

    var now = moment({hours:0, minutes:0, seconds:0, milliseconds:0});

    var antiguedad =  now.preciseDiff(fecha);
    
    //console.log("Antiguedad: ");
    //console.log(antiguedad);

        $("#antiguedad").html(antiguedad);
//        alert(antiguedad);
}

function calcular_edad(fecha)
{
    // Obtengo la fecha actual
    hoy=new Date()

    // Empleo la fecha que recibo como parametro
    // La descompongo en un array
    var array_fecha = fecha.split("-")
    //si el array no tiene tres partes, la fecha es incorrecta
    if (array_fecha.length!=3)
        return "La fecha introducida es incorrecta"

    //compruebo que los ano, mes, dia son correctos
    var ano
    ano = parseInt(array_fecha[2]);
    if (isNaN(ano))
        return "El año es incorrecto"

    var mes
    mes = parseInt(array_fecha[1]);
    if (isNaN(mes))
        return "El mes es incorrecto"

    var dia
    dia = parseInt(array_fecha[0]);
    if (isNaN(dia))
        return "El dia introducido es incorrecto"

    //si el año de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
    if (ano<=99){
        ano +=1900

    }

    //resto los años de las dos fechas
    edad=hoy.getFullYear()- ano - 1; //-1 porque no se si ha cumplido años ya este año

    //si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido
    if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
        return edad
    if (hoy.getMonth() + 1 - mes > 0)
        return edad+1

    //entonces es que eran iguales. miro los dias
    //si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
    if (hoy.getUTCDate() - dia >= 0)
        return edad + 1

    return edad
}

function calcular_antiguedad(fecha)
{
    hoy=new Date()
    var array_fecha = fecha.split("-")

    if (array_fecha.length!=3)
        return "La fecha introducida es incorrecta"

    var ano
    ano = parseInt(array_fecha[2]);
    if (isNaN(ano))
        return "El año es incorrecto"

    var mes
    mes = parseInt(array_fecha[1]);
    if (isNaN(mes))
        return "El mes es incorrecto"

    var dia

    dia = parseInt(array_fecha[0]);
    if (isNaN(dia))
        return "El dia introducido es incorrecto"

    if (ano<=99){
        ano +=1900

    }

    edad=hoy.getFullYear()- ano - 1; //-1 porque no se si ha cumplido años ya este año

    var meses

    if (hoy.getMonth() + 1 - mes > 0 ){
        meses=hoy.getMonth() + 1 - mes
        edad= edad+1
    }
    if(hoy.getMonth() + 1 - mes < 0)
    {
        meses=hoy.getMonth() + 1 - mes+12
    }
    if(hoy.getMonth() + 1 - mes == 0){
        meses=0
    }

    var dias
    //entonces es que eran iguales. miro los dias
    //si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
    if (hoy.getDate() - dia >= 0){
        dias=hoy.getDate() - dia
        if(meses==0){
            edad=edad + 1
        }
    }else{
        dias=hoy.getDate()-dia+30
    }

    var antiguedad=edad+ (edad=='1' ? " A&#241;o, " : " A&#241;os, ") +meses+ (meses=='1' ? " Mes y " : " Meses y ") +dias+ (dias=='1' ? " D&iacute;a" : " D&iacute;as")
    $("#antiguedad").html(antiguedad);
}

 function obtener_tabla_horario_puesto(registro)
 {
    console.log("obtener_tabla_horario_puesto");
    var tbody = "<tr>"+
                    "<td style='text-align: center'>" + registro.dia1_desde+"<br>-<br>"+registro.dia1_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia2_desde+"<br>-<br>"+registro.dia2_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia3_desde+"<br>-<br>"+registro.dia3_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia4_desde+"<br>-<br>"+registro.dia4_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia5_desde+"<br>-<br>"+registro.dia5_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia6_desde+"<br>-<br>"+registro.dia6_hasta+"</td>"+
                    "<td style='text-align: center'>" + registro.dia7_desde+"<br>-<br>"+registro.dia7_hasta+"</td>"+
                "</tr>";

    return tbody;
 }