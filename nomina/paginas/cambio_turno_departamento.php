<?php 
session_start();
ob_start();
?>
<?php
require_once '../lib/common.php';
include("../../includes/dependencias2.php");
//$_GET['estado'];

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);


$conexion=conexion();

$fecha1   = isset($_REQUEST['fecha1']) ? $_REQUEST['fecha1'] : NULL ;
$fecha2   = isset($_REQUEST['fecha2']) ? $_REQUEST['fecha2'] : NULL ;              
$turno   = isset($_REQUEST['turno']) ? $_REQUEST['turno'] : NULL ;
$anio   = isset($_REQUEST['anio']) ? $_REQUEST['anio'] : NULL ;
$categoria   = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : NULL ;
$departamento   = isset($_REQUEST['departamento']) ? $_REQUEST['departamento'] : NULL ;


if($fecha1!=NULL && $fecha2!=NULL && $turno!=NULL)
{
	$fecini = date("Y-m-d", strtotime($fecha1));
        $fecfin = date("Y-m-d", strtotime($fecha2));
        $conexion=conexion();
        $sql_columnas = "SELECT ficha, personal_id, cedula, apenom ";
        $sql_FROM = " FROM nompersonal ";
        $sql_WHERE = " WHERE estado NOT LIKE  '%Baja%' AND estado NOT LIKE  '%Egresado%' ";
        if ($departamento!="" && $departamento!=0 && $departamento!=NULL) 
        {
        $dept = explode(",", $departamento);
        
          $fragmento_sql.=" AND IdDepartamento in (".$departamento.")";
        
        }
        if ($categoria!="" && $categoria!=0 && $categoria!=NULL) {
            $fragmento_sql.=" AND codcat = '{$categoria}'";
        }
        
        $sql_WHERE .= $fragmento_sql;
        $sql_personal .= $sql_columnas;
        $sql_personal .= $sql_FROM;
        $sql_personal .= $sql_WHERE; 
//        echo $sql_personal;
//        exit;
        $resultado_personal = query($sql_personal,$conexion);
        while($fetch_personal = fetch_array($resultado_personal))
        {
            $ficha = $fetch_personal['ficha'];
            
            $consulta_delete="DELETE FROM nomcalendarios_personal WHERE ficha='$ficha' AND fecha>='$fecini' AND fecha<='$fecfin'";
//            echo $consulta_delete;
//            exit;
            $resultado_delete=query($consulta_delete,$conexion);
            
            $consulta_dias = "SELECT fecha, dia_fiesta "
                            . " FROM nomcalendarios_tiposnomina"
                            . " WHERE fecha>='$fecini' AND fecha<='$fecfin'";
            $resultado_dias = query($consulta_dias,$conexion);
            
            while($fetch_dias = fetch_array($resultado_dias))
            {
                $fecha = $fetch_dias['fecha'];
                $dia_fiesta = $fetch_dias['dia_fiesta'];
                $consulta_calendario="INSERT INTO nomcalendarios_personal "
                        . " (cod_empresa,"
                        . " ficha,"
                        . " fecha,"
                        . " dia_fiesta,"                        
                        . " turno_id)"
                        . " VALUES "
                        . "('1',"
                        . "'".$ficha."',"
                        . "'".$fecha."',"
                        . "'".$dia_fiesta."',"
                        . "'".$turno."')";
                $resultado_calendario=query($consulta_calendario,$conexion);
            }
        }
        if($resultado_calendario)
		echo "<script>alert('TURNOS ACTUALIZADOS EXITOSAMENTE');document.location.href = 'cambio_turno_departamento.php';</script>";
	else
		echo "<script>alert('¡ERROR AL ACTUALIZAR TURNOS!');</script>";
	
}

$bloques=3;

$consulta = "SELECT DISTINCT(YEAR(fecha)) as ano FROM nomcalendarios_tiposnomina";
$resultado = query($consulta,$conexion);


?>


<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
<style>


label.error {
    color: #b94a48;
}




.ms-container {
    width: 100%;
}

.ms-container .ms-list {
    height: 400px;
}

.ms-container .ms-selectable li.ms-elem-selectable, .ms-container .ms-selection li.ms-elem-selection {
    cursor: pointer;
}
</style>

<form action="" name="calper" id="calper" onsubmit=" if(confirm('¿Desea continuar con la operación?') == false) return false;" >
<div class="page-container">
        <!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
<div class="page-content">
	<div class="row">
		<div class="col-md-12">
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<label>Cambio de Turno</label>
					</div>
				</div>
				<div class="portlet-body">
						<br>
						<div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-2">
                                                        <label>A&#241;o:&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                                        </div> 
                                                        <div class="col-md-4"> 
                                                            <select name="anos" id="anos" class="form-control select2" onchange ="cambioanos('<?php echo $ficha ; ?>','<?php echo $turnoss ; ?>');">
                                                                    <OPTION >Seleccione a&#241;o</OPTION>
                                                                    <?php
                                                                    while($fetch = fetch_array($resultado))
                                                                    {
                                                                    ?>
                                                                    <option value="<?php echo $fetch['ano'];?>" <?php if($fetch['ano'] == $ano) echo 'selected'  ;   ?>><?php echo $fetch['ano'];?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                            </select>
                                                        </div> 
                                                    </div>
                                                </div>
                                                <br>
                    	
                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-2">
                                                        <label>Turno:</label>
                                                        </div> 
                                                        <div class="col-md-9"> 
                                                            <?php
                                                            $conexion=conexion();
                                                            $consulta="select turno_id,descripcion, date_format(entrada,'%h:%i:%s %p') as entrada, date_format(salida,'%h:%i:%s %p') as salida from nomturnos";
                                                            $result=query($consulta,$conexion);
                                                            ?>
                                                            <select name="turno" id="turno" class="form-control select2">
                                                                            <option value="">--Sin-Turno -----------------</option>
                                                                        <?php

                                                                            while($row = fetch_array($result))
                                                                            { 		
                                                                                    ?>
                                                                            <option <?php if($row[turno_id]==$turnoss) echo "selected";?> value="<?php echo $row[turno_id];?>"><?php printf("%s: Entrada: %s / Salida: %s", $row[descripcion],$row[entrada],$row[salida] ) ?></option>
                                                                            <?php 
                                                                            }
                                                                            ?>
                                                            </select>	
                                                        </div> 
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="form-group">
                                                            <label class="col-md-2 control-label">Fecha Inicio:</label>
                                                            <div class="col-md-4">
                                                                    <div class="input-group date date-picker" data-provide="datepicker">
                                                                            <input name="fecha1"  class="form-control" type="text" id="fecha1" required>
                                                                            <span class="input-group-btn">
                                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                    </div>
                                                            </div>	
                                                   
                                                            <label class="col-md-1 control-label">Fecha Fin:</label>
                                                            <div class="col-md-4">
                                                                    <div class="input-group date date-picker" data-provide="datepicker">
                                                                            <input name="fecha2"  class="form-control" type="text" id="fecha2" required>
                                                                            <span class="input-group-btn">
                                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                            </span>
                                                                    </div>
                                                            </div>	
                                                    </div>
                                                </div>
                                                <br>
                                                 <div class="row">
                                                    <div class="form-group">
											
                                                            <label class="control-label col-md-2">Departamento</label>

                                                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                                                    <?php
                                                                            $conexion=conexion();
                                                                            $consulta = "SELECT IdDepartamento,Descripcion
                                                                                            FROM   departamento 
                                                                                            ORDER BY Descripcion  ASC";	
                                                                            $result=query($consulta,$conexion);
                                                                            
                                                                            
                                                                    ?>
                                                                    <select multiple="multiple" class="multi-select" id="departamento" name="departamento[]">
                                                                            <?php
                                                                                    while($row = fetch_array($result))
                                                                                    {
                                                                                            ?> <option value="<?php echo $row[IdDepartamento]; ?>"><?php echo utf8_encode($row[Descripcion]); ?></option><?php
                                                                                    }
                                                                            ?>
                                                                    </select>
                                                            </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row">
                                                    <div class="form-group">
											
                                                            <label class="control-label col-md-2">Categoria</label>

                                                            <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                                                                    <?php
                                                                            $conexion=conexion();
                                                                            $consulta = "SELECT codorg,descrip
                                                                                            FROM   nomcategorias 
                                                                                            ORDER BY descrip  ASC";	
                                                                            $result=query($consulta,$conexion);
                                                                            
                                                                            
                                                                    ?>
                                                                    <select multiple="multiple" class="multi-select" id="categoria" name="categoria[]">
                                                                            <?php
                                                                                    while($row = fetch_array($result))
                                                                                    {
                                                                                            ?> <option value="<?php echo $row[codorg]; ?>"><?php echo utf8_encode($row[descrip]); ?></option><?php
                                                                                    }
                                                                            ?>
                                                                    </select>
                                                            </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <br>
                                                 <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-md-2">
                                                        
                                                        </div> 
                                                        <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9" align="center">
                                                                <input type="button" class="btn btn-sm blue" name="procesar" id="procesar"  value="Procesar">	
                                                        </div>
                                                    </div>
                                                </div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</form>
</body>
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#anos').select2();
    $('#turno').select2();
    $('#departamento').multiSelect();
    $('#categoria').multiSelect();
    $("#procesar").on("click",function()
	{
		var fecha1 = $("#fecha1").val();
                var fecha2 = $("#fecha2").val();	
                var departamento = $("#departamento").val();
		var categoria = $("#categoria").val();
                var turno = $("#turno").val();
		var anio = $("#anos").val();
       
		//console.log(posicion+" "+nombre+" "+apellido+" "+cargo+" "+funcion+" "+genero+" "+promocion );
                if(confirm('¿Desea continuar con la operación?') == false) return false;
		location.href ="cambio_turno_departamento.php?categoria="+categoria+"&departamento="+departamento+"&fecha1="+fecha1+"&fecha2="+fecha2+"&turno="+turno+"&anio="+anio;
	});
});
</script>