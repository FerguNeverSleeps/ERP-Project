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
	
	if (document.frmPrincipal.txtnombre.value==0){
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
		
		if(isset($_POST['chkAfiliado']))
			$afiliado=1;
		else
			$afiliado=0;

		/*$consulta="select * from nomfamiliares where cedula_beneficiario='".$_POST[txtcedula]."'";
		$resultado=sql_ejecutar($consulta);	
		if(num_rows($resultado)==0)
    {*/
    $costo = (( isset($_POST['txtcosto']) && !empty($_POST['txtcosto']) ) ? $_POST['txtcosto'] : 0);
    $cboGuarderia = (( isset($_POST['cboGuarderia']) && !empty($_POST['cboGuarderia']) ) ? $_POST['cboGuarderia'] : 0);
    $txtFechaBeca = (( isset($_POST['txtFechaBeca']) && !empty($_POST['txtFechaBeca']) ) ? $_POST['txtFechaBeca'] : '00-00-0000');

		$query="insert into nomfamiliares 
		(cedula,ficha,nombre,sexo,beneficiario,costo,nacionalidad,afiliado,fecha_nac,codpar,tipnom,cedula_beneficiario,apellido,niveledu,institucion,tallafranela,tallamono,codgua,fecha_beca)
		values ('$cedulatrab','$ficha','$_POST[txtnombre]','$_POST[optSexo]','$_POST[beneficiario]',
    '$costo','$_POST[optNacionalidad]','$afiliado','".fecha_sql($_POST['txtFechaNac'])."','".$_POST['parentesco']."','".$_SESSION['codigo_nomina']."',
    '$_POST[txtcedula]','$_POST[txtapellido]','$_POST[niveledu]','$_POST[txtinstitucion]','$_POST[tallafranela]','$_POST[tallamono]','$cboGuarderia',
    '".fecha_sql($txtFechaBeca)."')";
		
		$result=sql_ejecutar($query);	
		activar_pagina("familiares.php?txtficha=$ficha&cedula=$cedulatrab&bandera=$bandera");
		/*}
		else
		{
		mensaje("La cedula introducida ya esta siendo usada por otra persona. Por favor verifique los datos");
		}*/
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
		$query="select * from nomfamiliares where correl='$registro_id'";		
		$result=sql_ejecutar($query);	
		$row = fetch_array ($result);	
		$cedulatrab=$row[cedula];	
		$cedula=$row[cedula_beneficiario];	
		$nombre=$row[nombre];
		$apellido=$row[apellido];
		$nacionalidad=$row[nacionalidad];		
		$sexo=$row[sexo];
		$guarderia=$row[codgua];
		$costo=$row[costo];
		$afiliado=$row[afiliado];
		$fecha_nacimiento=fecha($row[fecha_nac]);
		$parentesco=$row[codpar];
		$correl=$row[correl];
		$nivel=$row[niveledu];
		$institucion=$row[institucion];
		$franela=$row[tallafranela];
		$mono=$row[tallamono];
		$promedionota=$row[promedionota];
		$beca=$row[beca];
                $discapacidad=$row[discapacidad];
                $vive=$row[vive];
		$fecha_beca=fecha($row[fecha_beca]);
                $beneficiario=$row[beneficiario];

	}	
		
	if ($op_tp==2){					
		
		if (isset($_POST['chkAfiliado'])){$afiliado=1;}else{$afiliado=0;}
                if (isset($_POST['chkDiscapacidad'])){$discapacidad=1;}else{$discapacidad=0;}
                if (isset($_POST['chkVive'])){$vive=1;}else{$vive=0;}
		if (isset($_POST['chkBeca'])){$beca=1;}else{$beca=0;}
		$query="UPDATE nomfamiliares set cedula_beneficiario='$_POST[txtcedula]',
		nombre='$_POST[txtnombre]',
		cedula='$_POST[cedula]',
		ficha='$_POST[txtficha]',
		apellido='$_POST[txtapellido]',
		nacionalidad='$_POST[optNacionalidad]',
		sexo='$_POST[optSexo]',
		costo='$_POST[txtcosto]',
		afiliado='$afiliado',
                vive='$vive',
                discapacidad='$discapacidad',
		fecha_nac='".fecha_sql($_POST['txtFechaNac'])."',
		codpar='".$_POST['parentesco']."',
		niveledu='".$_POST['niveledu']."',
		institucion='".$_POST['txtinstitucion']."',
		tallafranela='$_POST[tallafranela]',
		tallamono='$_POST[tallamono]',
		promedionota='$_POST[promedionota]',
		beca='$beca',
		codgua='$_POST[cboGuarderia]',
                beneficiario='$_POST[beneficiario]',
		fecha_beca='".fecha_sql($_POST['txtFechaBeca'])."'
		where correl='$_POST[correl]'";	
		//exit(0);
		$result=sql_ejecutar($query);				
		activar_pagina("familiares.php?txtficha=$ficha&cedula=$cedulatrab&bandera=$bandera");							
		{			
	}
}	
?>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
        <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
       <div class="row">
          <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue">
              <div class="portlet-title">
                <h4><?php if ($registro_id==0){echo "Agregar Familiar";}else{echo "Modificar Familiar";}?></h4>
              </div>
              <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Parentesco</label>
                        </div> 
                        <div class="col-md-3"> 
                            <SELECT name="parentesco" id="parentesco" class="form-control select2">
                              <?php $consulta="select * from nomparentescos";
                                    $resultado_parentesco=sql_ejecutar($consulta);
                                    while($fila_parentesco=fetch_array($resultado_parentesco))
                                    {
                              ?>
                                    <option <?if($parentesco==$fila_parentesco['codorg']){?> selected="true" <?}?> value="<?echo $fila_parentesco['codorg']?>"><?echo $fila_parentesco['descrip']; ?></option>
                              <?
                                    }
                              ?>
                            </SELECT>
                        </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Tipo Beneficiario:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <SELECT name="beneficiario" id="beneficiario" class="form-control select2">
                                <option <?if($beneficiario==0){?> selected="true" <?}?> value="<?echo 0?>"><?echo 'Ninguno'?></option>
                                <option <?if($beneficiario==1){?> selected="true" <?}?> value="<?echo 1?>"><?echo 'Principal'?></option>
                                <option <?if($beneficiario==2){?> selected="true" <?}?> value="<?echo 2?>"><?echo 'Contingente'?></option>
                            </SELECT>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>C&eacute;dula</label>
                      </div> 
                      <div class="col-md-3"> 
                            <input name="txtcedula" class="form-control" type="text" id="txtcedula" value="<?php if ($registro_id!=0){ echo $cedula; }  ?>" maxlength="20"><INPUT type="hidden" name="correl" value="<?echo $correl; ?>">   
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Nacionalidad:</label>
                      </div> 
                      <div class="radio-list col-md-4"> 
                          <input name="optNacionalidad" type="radio" value="V" <?php if ($nacionalidad=='V'){?> checked="checked"<?php }?>>Nacional
                          <input name="optNacionalidad" type="radio" value="E" <?php if ($nacionalidad=='E'){?> checked="checked"<?php }?>>Extranjero 
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Nombre:</label>
                      </div> 
                      <div class="col-md-3"> 
                         <input name="txtnombre" type="text" id="txtnombre" class="form-control" value="<?php if ($registro_id!=0){ echo utf8_encode($nombre); }  ?>">
                      </div> 
                    </div>
                    <br>
                     <div class="row">
                      <div class="col-md-3">
                            <label>Apellido:</label>
                      </div> 
                      <div class="col-md-3"> 
                        <input name="txtapellido" type="text" id="txtapellido" class="form-control" value="<?php if ($registro_id!=0){ echo utf8_encode($apellido); }  ?>">
                      </div> 
                    </div>
                     <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Sexo:</label>
                      </div> 
                      <div class="col-md-3"> 
                          <input name="optSexo" type="radio" value="Masculino"<?php  if($sexo=="Masculino"){?> checked="checked"<?php }?>>Masculino
                          <input name="optSexo" type="radio" value="Femenino"<?php if ($sexo=="Femenino"){?> checked="checked"<?php }?>>Femenino          
                      </div> 
                    </div>
                     <br>
                     <div class="row">
                      <div class="col-md-3">
                            <label>Discapacidad:</label>
                      </div>
                      <div class="col-md-3"> 
                         <input name="chkDiscapacidad" type="checkbox" id="chkDiscapacidad" value="checkbox"
                          <?php if ($discapacidad==1){?> checked="checked"<?php }?>>
                          <label>Marque si es discapacitado</label>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Vive:</label>
                      </div>
                      <div class="col-md-3"> 
                         <input name="chkVive" type="checkbox" id="chkVive" value="checkbox"
                          <?php if ($vive==1){?> checked="checked"<?php }?>>
                          <label>Marque si vive</label>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Afiliado:</label>
                      </div>
                      <div class="col-md-3"> 
                         <input name="chkAfiliado" type="checkbox" id="chkAfiliado" value="checkbox"
                          <?php if ($afiliado==1){?> checked="checked"<?php }?>>
                          <label>Marque si es afiliado</label>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Fecha de Nacimiento:</label>
                      </div>
                      <div class="col-md-3">
                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy"> 
                         <input name="txtFechaNac" type="text" id="txtFechaNac" class="form-control" value="<?php if ($registro_id!=0){ echo $fecha_nacimiento; }  ?>" maxlength="60" >
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                      </div> 
                    </div>
                    <br>
                     <div class="row">
                      <div class="col-md-3">
                            <label>Guarderia:</label>
                      </div> 
                      <div class="col-md-3"> 
                          <select name="cboGuarderia" id="cboGuarderia"  class="form-control select2" >
                                <option value="0">SELECCIONE</option>
                                <?php 
                                $query="select codorg,descrip from nomguarderias";
                                $result=sql_ejecutar($query);
                              //ciclo para mostrar los datos
                                while ($row = fetch_array($result))
                                {     
                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                   if($row[codorg]==$guarderia)
                                   { 
                                ?>
                                  <option value="<?php echo $row[codorg];?>" selected > <?php echo $row[descrip];?> </option>
                                  <?php 
                                   }
                                   else // opcion de agregar
                                   { 
                                  ?>
                                   <option value="<?php echo $row[codorg];?>"><?php echo $row[descrip];?></option>
                                    <?php 
                                   } 
                                }//fin del ciclo while
                                    ?>
                          </select>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Costo de Guarderia:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <input name="txtcosto" type="text" id="txtcosto" class="form-control" value="<?php if ($registro_id!=0){ echo $costo; }  ?>" maxlength="60" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Nivel Educativo:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <SELECT name="niveledu" id="niveledu" class="form-control select2">
                                <option <?if($nivel=='Prescolar'){?> selected="true" <?}?> value="<?echo 'Prescolar'?>"><?echo 'Prescolar'?></option>
                                <option <?if($nivel=='Primaria'){?> selected="true" <?}?> value="<?echo 'Primaria'?>"><?echo 'Primaria'?></option>
                                <option <?if($nivel=='Basico'){?> selected="true" <?}?> value="<?echo 'Basico'?>"><?echo 'Basico'?></option>
                                <option <?if($nivel=='Diversificado'){?> selected="true" <?}?> value="<?echo 'Diversificado'?>"><?echo 'Diversificado'?></option>
                                <option <?if($nivel=='Universitario'){?> selected="true" <?}?> value="<?echo 'Universitario'?>"><?echo 'Universitario'?></option>
                            </SELECT>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Institución:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <input name="txtinstitucion" type="text" id="txtinstitucion" class="form-control" value="<?php if ($registro_id!=0){ echo $institucion; }  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Beca:</label>
                      </div> 
                      <div class="col-md-4"> 
                             <input name="chkBeca" type="checkbox" id="chkBeca" value="checkbox"<?php if ($beca==1){?> checked="checked"<?php }?>>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Fecha Nota:</label>
                      </div> 
                      <div class="col-md-3">
                          <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy"> 
                            <input name="txtFechaBeca" type="text" id="txtFechaBeca" class="form-control" value="<?php if ($registro_id!=0){ echo $fecha_beca; }  ?>" maxlength="60" >
                            <span class="input-group-btn">
                              <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                            </span>
                          </div>
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Promedio de Notas:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <input name="promedionota" type="text" id="promedionota" class="form-control" value="<?php if ($registro_id!=0){ echo $promedionota; }  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Talla Franela:</label>
                      </div> 
                      <div class="col-md-3"> 
                            <input name="tallafranela" type="text" id="tallafranela" class="form-control" value="<?php if ($registro_id!=0){ echo $franela; }  ?>">
                      </div> 
                    </div>
                    <br>
                    <div class="row">
                      <div class="col-md-3">
                            <label>Talla Mono:</label>
                      </div> 
                      <div class="col-md-3"> 
                          <input name="tallamono" type="text" id="tallamono" class="form-control" value="<?php if ($registro_id!=0){ echo $mono; }  ?>">
                      </div> 
                    </div>
                    <div class="actions" align="right">
                        <a class="btn btn-sm blue"  href="javascript: Enviar(); document.frmPrincipal.submit();">
                        <i class="fa fa-plus"></i>
                          Guardar
                        </a>
                        <a class="btn btn-sm blue"  href="javascript: history.back();">
                        <i class="fa fa-plus"></i>
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
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {     
   
    $("#parentesco").select2();
    $("#cboGuarderia").select2();
    $("#niveledu").select2();
    $("#beneficiario").select2();
    
  });
</script>
</body>
</html>