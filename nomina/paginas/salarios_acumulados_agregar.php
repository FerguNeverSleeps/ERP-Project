<?php 
session_start();
ob_start();
	include("func_bd.php");	
  include ("../header4.php");
	include ("../lib/common.php");
?>

<script>
function Enviar(){					
	if (document.frmPrincipal.registro_id.value==0){ 

		document.frmPrincipal.op_tp.value=1}
	else{ 	

		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.fecha_pago.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar un nombre valido. Verifique...");}
}

</script>

<?php 
$registro_id=$_POST[registro_id];
$bandera=$_POST['bandera'];
if (isset($HTTP_GET_VARS[txtficha]))
{
	$ficha=$HTTP_GET_VARS[txtficha];
	$cedula=$HTTP_GET_VARS[cedula];	
}
else
{
	$ficha=$_POST[txtficha];
	$cedulatrab=$_POST[cedula];
}
	
	$op_tp=$_POST[op_tp];
	$validacion=0;
	
	if ($registro_id==0) 
	{// Si el registro_id es 0 se va a agregar un registro nuevo
		if ($op_tp==1)
		{
		
		
		$query="INSERT INTO salarios_acumulados 
        (
        ficha,
        cedula,
        fecha_pago,
        cod_planilla,
        tipo_planilla,
        frecuencia_planilla,
        salario_bruto,
        desc_empresa,
        viaticos,
        comisiones,
        gratificaciones,
        donaciones,
        vacac,
        xiii,
        gtorep,
        xiii_gtorep,
        liquida,
        bono,
        otros_ing,
        prima,
        s_s,
        s_e,
        islr,
        islr_gr,
        acreedor_suma,
        Neto)
        values 
        (
        '$ficha',"
        . "'$cedulatrab',"
        . "'".fecha_sql($_POST['fecha_pago'])."',"
        . "'$_POST[cod_planilla]',"
        . "'$_POST[tipo_planilla]',"
        . "'$_POST[frecuencia_planilla]',"
        . "'$_POST[salario_bruto]',"
        . "'$_POST[desc_empresa]',"
        . "'$_POST[viaticos]',"
        . "'$_POST[comisiones]',"
        . "'$_POST[gratificaciones]',"
        . "'$_POST[donaciones]',"
        . "'$_POST[vacac]',"
        . "'$_POST[xiii]',"
        . "'$_POST[gtorep]',"
        . "'$_POST[xiii_gtorep]',"
        . "'$_POST[liquida]',"
        . "'$_POST[bono]',"
        . "'$_POST[otros_ing]',"
        . "'$_POST[prima]',"
        . "'$_POST[s_s]',"
        . "'$_POST[s_e]',"
        . "'$_POST[islr]',"
        . "'$_POST[islr_gr]',"
        . "'$_POST[acreedor_suma]',"
        . "'$_POST[Neto]')";
		$result=sql_ejecutar($query);	
		activar_pagina("salarios_acumulados_ajax.php?txtficha=$ficha&cedula=$cedulatrab&bandera=$bandera");
		/*}
		else
		{
		mensaje("La cedula introducida ya esta siendo usada por otra persona. Por favor verifique los datos");
		}*/
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
		$query="SELECT * FROM salarios_acumulados WHERE id='$registro_id'";		
		$result=sql_ejecutar($query);	
		$row = fetch_array ($result);	
    $ficha               = $row[ficha];
    $cedula              = $row[cedula];
    $fecha_pago          = fecha($row[fecha_pago]);
    $cod_planilla        = $row[cod_planilla];
    $tipo_planilla       = $row[tipo_planilla];
    $frecuencia_planilla = $row[frecuencia_planilla];
    $salario_bruto       = $row[salario_bruto];
    $vacac               = $row[vacac];
    $xiii                = $row[xiii];
    $gtorep              = $row[gtorep];
    $xiii_gtorep         = $row[xiii_gtorep];
    $liquida             = $row[liquida];
    $desc_empresa        = $row[desc_empresa];
    $viaticos            = $row[viaticos];
    $comisiones          = $row[comisiones];
    $gratificaciones     = $row[gratificaciones];
    $donaciones          = $row[donaciones];
    $bono                = $row[bono];
    $otros_ing           = $row[otros_ing];
    $prima               = $row[prima];
    $s_s                 = $row[s_s];
    $s_e                 = $row[s_e];
    $islr                = $row[islr];
    $islr_gr             = $row[islr_gr];
    $acreedor_suma       = $row[acreedor_suma];
    $Neto                = $row[Neto];

	}	
		
	if ($op_tp==2){					
		
		
		$query="UPDATE salarios_acumulados SET
      fecha_pago          = '".fecha_sql($_POST['fecha_pago'])."',
      cod_planilla        = '$_POST[cod_planilla]',
      tipo_planilla       = '$_POST[tipo_planilla]',
      frecuencia_planilla = '$_POST[frecuencia_planilla]',
      salario_bruto       = '$_POST[salario_bruto]',
      desc_empresa        = '$_POST[desc_empresa]',
      viaticos            = '$_POST[viaticos]',
      comisiones          = '$_POST[comisiones]',
      gratificaciones     = '$_POST[gratificaciones]',
      donaciones          = '$_POST[donaciones]',
      vacac               = '$_POST[vacac]',
      xiii                = '$_POST[xiii]',
      gtorep              = '".$_POST['gtorep']."',
      xiii_gtorep         = '".$_POST['xiii_gtorep']."',
      liquida             = '".$_POST['liquida']."',
      bono                = '$_POST[bono]',
      otros_ing           = '$_POST[otros_ing]',
      prima               = '$_POST[prima]',
      s_s                 = '$_POST[s_s]',
      s_e                 = '$_POST[s_e]',
      islr                = '$_POST[islr]',
      islr_gr             = '$_POST[islr_gr]',
      acreedor_suma       = '$_POST[acreedor_suma]',
      Neto                = '$_POST[Neto]'
		where id='$registro_id'";	
		//exit(0);
		$result=sql_ejecutar($query);				
		activar_pagina("salarios_acumulados_ajax.php?txtficha=$ficha&cedula=$cedulatrab&bandera=$bandera");							
					
	}

?>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<form action="" method="post" name="frmPrincipal" id="frmPrincipal" autocomplete="off">
<div class="page-container">
        <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
       <div class="row">
          <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue">
              <div class="portlet-title">
                <h4><?php if ($registro_id==0){echo "Salario Acumulado - Agregar";}else{echo "Salario Acumulado - Modificar";}?></h4>
              </div>
              <div class="portlet-body" >
                     <br>    
                    <div class="row">
                      <div class="col-md-2">
                            <label>Fecha:</label>
                      </div>
                      <div class="col-md-3">
                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy"> 
                         <input name="fecha_pago" type="text" id="fecha_pago" class="form-control" value="<?php if ($registro_id!=0){ echo $fecha_pago; }  ?>" maxlength="60" >
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Tipo Planilla:</label>
                      </div> 
                      <div class="col-md-3"> 
                          <select name="tipo_planilla" id="tipo_planilla" class="form-control select2" >
                                <option value=""  > Seleccione </option>
                                <?php 
                                $query="select codtip,descrip from nomtipos_nomina";
                                $result=sql_ejecutar($query);
                              //ciclo para mostrar los datos
                                while ($row = fetch_array($result))
                                {     
                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                   if($row[codtip]==$tipo_planilla)
                                   { 
                                ?>
                                  <option value="<?php echo $row[codtip];?>" selected > <?php echo $row[descrip];?> </option>
                                  <?php 
                                   }
                                   else // opcion de agregar
                                   { 
                                  ?>
                                   <option value="<?php echo $row[codtip];?>"><?php echo $row[descrip];?></option>
                                    <?php 
                                   } 
                                }//fin del ciclo while
                                    ?>
                          </select>
                      </div>
                      <div class="col-md-1">
                            <label>Frecuencia:</label>
                      </div> 
                      <div class="col-md-3"> 
                          <select name="frecuencia_planilla" id="frecuencia_planilla" class="form-control select2" >
                              <option value=""  > Seleccione </option>
                                <?php 
                                $query="select codfre,descrip from nomfrecuencias";
                                $result=sql_ejecutar($query);
                              //ciclo para mostrar los datos
                                while ($row = fetch_array($result))
                                {     
                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                   if($row[codfre]==$frecuencia_planilla)
                                   { 
                                ?>
                                  <option value="<?php echo $row[codfre];?>" selected > <?php echo $row[descrip];?> </option>
                                  <?php 
                                   }
                                   else // opcion de agregar
                                   { 
                                  ?>
                                   <option value="<?php echo $row[codfre];?>"><?php echo $row[descrip];?></option>
                                    <?php 
                                   } 
                                }//fin del ciclo while
                                    ?>
                          </select>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Planilla:</label>
                      </div> 
                      <div class="col-md-7"> 
                          <select name="cod_planilla" id="cod_planilla" class="form-control select2" >
                              <option value=""  > Seleccione </option>
                                <?php 
                                $query="select codnom,descrip from nom_nominas_pago";
                                $result=sql_ejecutar($query);
                              //ciclo para mostrar los datos
                                while ($row = fetch_array($result))
                                {     
                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                   if($row[codnom]==$cod_planilla)
                                   { 
                                ?>
                                  <option value="<?php echo $row[codnom];?>" selected > <?php echo $row[descrip];?> </option>
                                  <?php 
                                   }
                                   else // opcion de agregar
                                   { 
                                  ?>
                                   <option value="<?php echo $row[codnom];?>"><?php echo $row[descrip];?></option>
                                    <?php 
                                   } 
                                }//fin del ciclo while
                                    ?>
                          </select>
                      </div>                       
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Salario Bruto:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="salario_bruto" class="form-control" type="text" id="salario_bruto" value="<?php if ($registro_id!=0){ echo $salario_bruto; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div> 
                      <div class="col-md-2">
                            <label>Seguro Social:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="s_s" class="form-control" type="text" id="s_s" value="<?php if ($registro_id!=0){ echo $s_s; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div> 
                    </div>
                    <br>                    
                    <div class="row">
                      <div class="col-md-2">
                            <label>Acum. Vac.:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="vacac" type="text" id="vacac" class="form-control" value="<?php if ($registro_id!=0){ echo $vacac; }else{ echo 0 ;}  ?>">
                      </div> 
                      <div class="col-md-2">
                            <label>Seguro Educativo:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="s_e" class="form-control" type="text" id="s_e" value="<?php if ($registro_id!=0){ echo $s_e; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>XIII:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="xiii" type="text" id="xiii" class="form-control" value="<?php if ($registro_id!=0){ echo $xiii; }else{ echo 0 ;}  ?>">
                      </div> 
                      <div class="col-md-2">
                            <label>ISLR:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="islr" class="form-control" type="text" id="islr" value="<?php if ($registro_id!=0){ echo $islr; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div> 
                    </div>
                    <br>       
                    <div class="row">
                      <div class="col-md-2">
                            <label>Gasto Rep-:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="gtorep" type="text" id="gtorep" class="form-control" value="<?php if ($registro_id!=0){ echo $gtorep; }else{ echo 0 ;}  ?>">
                      </div> 
                      <div class="col-md-2">
                            <label>ISLR G.R.:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="islr_gr" class="form-control" type="text" id="islr_gr" value="<?php if ($registro_id!=0){ echo $islr_gr; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div> 
                    </div>
                    <br>       
                    <div class="row">
                      <div class="col-md-2">
                            <label>XIII Gasto Rep.:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="xiii_gtorep" type="text" id="xiii_gtorep" class="form-control" value="<?php if ($registro_id!=0){ echo $xiii_gtorep; }else{ echo 0 ;}  ?>">
                      </div> 
                      <div class="col-md-2">
                            <label>Acreedor Suma:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="acreedor_suma" class="form-control" type="text" id="acreedor_suma" value="<?php if ($registro_id!=0){ echo $acreedor_suma; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Liquidacion:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="liquida" type="text" id="liquida" class="form-control" value="<?php if ($registro_id!=0){ echo $liquida; }else{ echo 0 ;}  ?>">
                      </div> 
                      <div class="col-md-2">
                            <label>Desc. Empresa:</label>
                      </div> 
                      <div class="col-md-2"> 
                            <input name="desc_empresa" class="form-control" type="text" id="desc_empresa" value="<?php if ($registro_id!=0){ echo $desc_empresa; }else{ echo 0 ;}  ?>" maxlength="10">
                      </div>
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Viáticos:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="viaticos" type="text" id="viaticos" class="form-control" value="<?php if ($registro_id!=0){ echo $viaticos; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Comisiones:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="comisiones" type="text" id="comisiones" class="form-control" value="<?php if ($registro_id!=0){ echo $comisiones; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Gratificaciones:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="gratificaciones" type="text" id="gratificaciones" class="form-control" value="<?php if ($registro_id!=0){ echo $gratificaciones; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-2">
                            <label>Bonificaciones:</label>
                      </div> 
                      <div class="col-md-2"> 
                        <input name="bono" type="text" id="bono" class="form-control" value="<?php if ($registro_id!=0){ echo $bono; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br> 
                    <div class="row">
                      <div class="col-md-2">
                            <label>Prima:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="prima" type="text" id="prima" class="form-control" value="<?php if ($registro_id!=0){ echo $prima; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>  
                    <div class="row">
                      <div class="col-md-2">
                            <label>Otros Ingresos:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="otros_ing" type="text" id="otros_ing" class="form-control" value="<?php if ($registro_id!=0){ echo $otros_ing; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>  
                    <div class="row">
                      <div class="col-md-2">
                            <label>Donaciones:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="donaciones" type="text" id="donaciones" class="form-control" value="<?php if ($registro_id!=0){ echo $donaciones; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>  
                    <div class="row">
                      <div class="col-md-2">
                            
                      </div> 
                      <div class="col-md-2"> 
                         
                      </div> 
                      <div class="col-md-2">
                            <label>Neto:</label>
                      </div> 
                      <div class="col-md-2"> 
                         <input name="Neto" type="text" id="Neto" class="form-control" value="<?php if ($registro_id!=0){ echo $Neto; }else{ echo 0 ;}  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="actions" align="center">
                        <a class="btn btn-sm blue"  href="javascript: Enviar(); document.frmPrincipal.submit();">
                        <i class="fa fa-check"></i>
                          Guardar
                        </a>
                        <a class="btn btn-sm red"  href="javascript: history.back();">
                        <i class="fa fa-ban"></i>
                          Cancelar
                        </a>
                    </div>
                
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>
</div>
  <p>
    <input name="op_tp" type="Hidden" id="op_tp" value="-1">
    <input name="cedula" type="Hidden" id="cedula" value="<?php echo $cedulatrab; ?>">
    <input name="registro_id" type="hidden" id="registro_id" value="<?php echo $registro_id; ?>">
    <input name="txtficha" type="hidden" id="txtficha" value="<?php echo $ficha; ?>">
    <input name="bandera" type="hidden" id="bandera" value="<?php echo $bandera; ?>">
  </p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>  
  <p>&nbsp;</p>
</form>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
</body>
</html>