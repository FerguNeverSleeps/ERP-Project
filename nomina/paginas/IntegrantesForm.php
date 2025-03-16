<?php
session_start();
ob_start();

$termino = $_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
include("../header.php");
?>






<?php
$registro_id = $_POST[registro_id];
$op_tp = $_POST[op_tp];
$fecha_actual = date("Y-m-d");
$validacion = 0;


if ($filaempresa['tipoficha'] == 1) {
    $consulta = "select max(ficha) as valor from nompersonal where tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $result = sql_ejecutar($consulta);

    $fila = mysqli_fetch_array($result);
    $actual = $fila['valor'] + 1;
} else {

    $consulta = "select max(ficha) as valor from nompersonal";
    $result = sql_ejecutar($consulta);

    $fila = mysqli_fetch_array($result);
    $actual = $fila['valor'] + 1;
}
if(isset($_GET['ficha'])){$actual = $_GET['ficha'];}
if(isset($_GET['edit'])){
    $add_param_url="?edit&ficha=".$actual;
    $editarFicha = 1;
    $tipo_operacion="edit";
    $consulta = "select cedula as valor from nompersonal where ficha='" . $actual . "'";
    $result = sql_ejecutar($consulta);
    $fila = mysqli_fetch_array($result);
    $cedula_actual = $fila['valor'];
}else{
    $editarFicha=0;
    $tipo_operacion="add";
    $add_param_url="";
}

$query = "select * from nomempresa";
$result = sql_ejecutar($query);
$row_emp = mysqli_fetch_array($result);

$sUsername = $_SESSION['usuario'];
$query = "select * from ".SELECTRA_CONF_PYME.".nomusuarios where login_usuario='$sUsername'";
$resultUser = sql_ejecutar($query);
$row_usuario = mysqli_fetch_array($resultUser);

?>
<!DOCTYPE HTML >
<html>
<head>
    <title></title>
    <link href="../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css" rel="stylesheet" />
    <style type="text/css">
	        
            .editar{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/pencil.png);
            }
            .calendario{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/calendar.png);
            }
            .cargas_familiares{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/group.png);
            }
            .campos_adicionales{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/table_multiple.png);
            }
            .imprimir{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/printer.png);
            }
            .expediente{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/folder_page.png);
            }
            .eliminar{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/delete.png);
            }

            #horario_puesto-bodyEl{
                height: 120px;
            }

            #horario_puesto-bodyEl.x-form-item-body{
                vertical-align: top;
            }

            #horario_puesto-inputEl, #horario_puesto-labelEl>span{
                margin-top: 20px;
            }
		</style>


    <script src="../../includes/js/ext-5.0.0/build/ext-all-debug.js"></script>
    <script src="../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/ext-theme-neptune.js"></script>
    <script type="text/javascript" src="../../includes/js/ext-5.0.0/build/packages/ext-locale/build/ext-locale-es.js"></script>
    <script>
    	var TIPO_VIEW = "form";
    	var ACTUAL = "<? echo $actual?>";
        var CEDULA_ACTUAL = "<? echo $cedula_actual?>";
    	var EDITAR_PERSONAL = "<? echo $editarFicha?>";
    	var OPERACION =  "<? echo $tipo_operacion?>";
        var RELOAD_URL_ADD = "<? echo $add_param_url?>";

        var TIPO_EMPRESA = "<? echo $row_emp[tipo_empresa]?>";
    	var nivel1 = ("<? echo $row_emp[nivel1]?>" != "1")?true:false;
    	var nomNivel1 = "<?php echo $row_emp[nomniv1]; ?>";
    	var nivel2 = ("<? echo $row_emp[nivel2]?>" != "1")?true:false;
    	var nomNivel2 = "<?php echo $row_emp[nomniv2]; ?>";
    	var nivel3 = ("<? echo $row_emp[nivel3]?>" != "1")?true:false;
    	var nomNivel3 = "<?php echo $row_emp[nomniv3]; ?>";
    	var nivel4 = ("<? echo $row_emp[nivel4]?>" != "1")?true:false;
    	var nomNivel4 = "<?php echo $row_emp[nomniv4]; ?>";
    	var nivel5 = ("<? echo $row_emp[nivel5]?>" != "1")?true:false;
    	var nomNivel5 = "<?php echo $row_emp[nomniv5]; ?>";
    	var nivel6 = ("<? echo $row_emp[nivel6]?>" != "1")?true:false;
    	var nomNivel6 = "<?php echo $row_emp[nomniv6]; ?>";
    	var nivel7 = ("<? echo $row_emp[nivel7]?>" != "1")?true:false;
    	var nomNivel7 = "<?php echo $row_emp[nomniv7]; ?>";

        var USUARIO_ACCESO_SUELDO = "<?php echo $row_usuario[acceso_sueldo]; ?>";

    </script>
    <script type="text/javascript">
    function cambioanos(fichas,turnoss) 
    {

    var anosx = document.getElementById("anos").value ;
    var fichas = fichas;
    var turno =  turnoss;
    Ext.getCmp('tab-calendario').loader.url = "calendarios_personal_ajax.php?ano="+anosx+"&ficha="+fichas+"&turnoss="+ turno;
    Ext.getCmp('tab-calendario').loader.load();
    //document.location.href = "?ano="+anosx+"&ficha="+fichas+"&turnoss="+ turno ;

    }

    function calcular_antiguedad(fecha){
    	//console.log(fecha)
        hoy=new Date()
        var array_fecha = fecha.split("/")

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

        var antiguedad=edad+" A&#241;os, "+meses+" Meses y "+dias+" Dias"
        Ext.getCmp("antiguedad").setValue("<strong>"+antiguedad+"</strong>");
        //document.getElementById('antiguedad').innerHTML="<strong>"+antiguedad+"</strong>"
    }
    function actualizar(valor1){
        //var temp1=document.getElementById(valor1)
        //var temp2=document.getElementById(valor2)

        var edad=calcular_edad(valor1)
        var cadena=edad.toString()
        //temp2.innerHTML="<strong>"+cadena+" A&#241;os</strong>"
        Ext.getCmp("edad").setValue("<strong>"+cadena+" A&#241;os</strong>");
    }

    function calcular_edad(fecha){

        //calculo la fecha de hoy
        hoy=new Date()
        //alert(hoy)

        //calculo la fecha que recibo
        //La descompongo en un array
        var array_fecha = fecha.split("/")
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


    function Actualizar_Foto() {
        var archivo=document.getElementById("mifichero").value
        var long=archivo.length
        var i=0,cont
        cont=long

        var cadena_final="fotos/"+archivo.substr(cont,i)

        document.getElementById("imgFoto").src=archivo
        document.getElementById("imgFoto").lowsrc=archivo//cadena_final//document.getElementById("mifichero").value;

        document.getElementById("txtrutafoto").value=archivo//cadena_final//document.getElementById("mifichero").value;

    }

    function obtener_tabla_horario_puesto(record)
    {
        var tabla = "<table border='1' cellpadding='5' cellspacing='0' style='width: 100%'>" +
                        "<thead><tr>"+
                            "<th style='text-align: center'>Lunes</th>"+
                            "<th style='text-align: center'>Martes</th>"+
                            "<th style='text-align: center'>Miércoles</th>"+
                            "<th style='text-align: center'>Jueves</th>"+
                            "<th style='text-align: center'>Viernes</th>"+
                            "<th style='text-align: center'>Sábado</th>"+
                            "<th style='text-align: center'>Domingo</th>"+
                        "</tr></thead>"+
                        "<tbody><tr>"+
                            "<td style='text-align: center'>"+record.dia1_desde+"<br>-<br>"+record.dia1_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia2_desde+"<br>-<br>"+record.dia2_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia3_desde+"<br>-<br>"+record.dia3_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia4_desde+"<br>-<br>"+record.dia4_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia5_desde+"<br>-<br>"+record.dia5_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia6_desde+"<br>-<br>"+record.dia6_hasta+"</td>"+
                            "<td style='text-align: center'>"+record.dia7_desde+"<br>-<br>"+record.dia7_hasta+"</td>"+
                        "</tr></tbody>"+
                    "</table>";
         return tabla;
    }
</script>
    <script src="../../includes/js/gui/Personal/Personal.js"></script>
</head>
<body>

</body>
</html>

