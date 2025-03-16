<?php 
session_start();
ob_start();
?>
<?php 
require_once '../lib/common.php';
include ("../header4.php");
include ("funciones_nomina.php");
$url="movimientos_nomina_pago_agregar1.php";
$modulo="Movimientos Planilla - Agregar";
$host_ip = $_SERVER['REMOTE_ADDR'];

$tabla="nomconceptos";
$titulos=array("Tipo de Concepto","Codigo de Concepto","Descripción","Un.","Referencia");
$indices=array("2","0","1","3");

$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$ficha=$_GET['ficha'];
$pagina=$_GET['pagina'];
$todo=$_GET['todo'];

if(!isset($_POST['nomina']))
{
	$nombre_nomina=$_GET['nomina'];
}
else
{
	$nombre_nomina=$_POST['nomina'];
}
if(!isset($_POST['ficha']))
{
	$ficha=$_GET['ficha'];
}
else
{
	$ficha=$_POST['ficha'];
}

$conexion=conexion();
$consulta="select * from nom_nominas_pago where codnom='".$nombre_nomina."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado_nom=query($consulta,$conexion);
$fila_nom=fetch_array($resultado_nom);
$CODNOM=$nombre_nomina;
$FECHANOMINA=$fila_nom['periodo_ini'];
$FECHAFINNOM=$fila_nom['periodo_fin'];
$LUNES=lunes($FECHANOMINA);	
$LUNESPER=lunes_per($FECHANOMINA,$FECHAFINNOM);
$consulta="select monsalmin from nomempresa";
$resultado_salmin=query($consulta,$conexion);
$fila_salmin=fetch_array($resultado_salmin);


$consulta="select * from nompersonal where ficha='".$ficha."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado=query($consulta,$conexion);
$fila=fetch_array($resultado);
$CEDULA = $fila[cedula];
$FICHA = $fila[ficha];
$SUELDO=$fila[suesal];//LISTO
$SEXO=".".$fila[sexo]."'";
$FECHANACIMIENTO=date("d/m/Y",strtotime($fila[$fecnac]));
$EDAD=date("Y")-date("Y",$fila[$fecnac]);
$TIPONOMINA=$fila[tipnom];//LISTO
$FECHAINGRESO=$fila[fecing];//LISTO
$CODPROFESION=$fila[codpro];
$HORABASE=$fila[hora_base];
$CODCATEGORIA=$fila[codcat];
$CODCARGO=$fila[codcargo];
$SITUACION=$fila[estado];
$SUELDOPROPUESTO=$fila[sueldopro];
$TIPOCONTRATO=$fila[contrato];
$FORMACOBRO=$fila[forcob];
$NIVEL1=$fila[codnivel1];
$NIVEL2=$fila[codnivel2];
$NIVEL3=$fila[codnivel3];
$NIVEL4=$fila[codnivel4];
$NIVEL5=$fila[codnivel5];
$NIVEL6=$fila[codnivel6];
$NIVEL7=$fila[codnivel7];
$FECHAAPLICACION=$fila[fechaplica];
$TIPOPRESENTACION=$fila[tipopres];
$FECHAFINSUS=$fila[fechasus];
$FECHAINISUS=$fila[fechareisus];
$FECHAFINCONTRATO=$fila[fecharetiro];
$FECHAVAC=$fila[fechavac];
$FECHAREIVAC=$fila[fechareivac];
$CONTRACTUAL=$fila[contractual];
$PRT=$fila[proratea];
$REF=0;

$nombre=       $fila[apenom];
$cedula=       $fila[cedula];
$nomina=$nombre_nomina;
$tipo_nomina=$_SESSION['codigo_nomina'];
$accion="agregar";

$consulta_nivel="select * from nomnivel1";
$resultado_nivel=query($consulta_nivel,$conexion);
//$fila_nivel=fetch_array($resultado);

$descripcion_log = "Agregar Movimientos - Colaborador ".$ficha." - Nombre ".$nombre." - Cedula: ".$cedula;

$SALARIOMIN=$fila_salmin['monsalmin'];

if(isset($_POST['opcion']) and $_POST['opcion']=="guardar")
{
	$temp_des=$_POST['descripcion'];
	$i=0;
	foreach($temp_des as $des)
	{
		$descripcion[$i]=$des;
		$i++;
	}
	
	$temp_concepto=$_POST['codcon'];
	$i=0;
	foreach($temp_concepto as $des)
	{
		$concepto[$i]=$des;
		$i++;
	}
	$tipo=$_POST['tipcon'];
	$i=0;
	foreach($tipo as $des)
	{
		$tipoconcepto[$i]=$des;
		$i++;
	}
	$temp_unidad=$_POST['unidad'];
	$i=0;
	foreach($temp_unidad as $des)
	{
		$unidad[$i]=$des;
		$i++;
	}
	/*$temp_formula=$_POST['formula'];
	$i=0;
	foreach($temp_formula as $des){
		$formula[$i]=$des;
		$i++;
	}*/
	$temp_ref=$_POST['referencia'];
	$i=0;
	foreach($temp_ref as $des)
	{
		$ref[$i]=$des;
		$i++;
	}
	if($SITUACION!="Inactivo")
	{
		foreach($_POST['seleccion'] as $valor)
		{
			$consulta_mov="select * from nom_movimientos_nomina where codcon='".$concepto[$valor]."' and codnom='".$_POST['nomina']."' and ficha ='".$_POST['ficha']."' and tipnom='".$_SESSION['codigo_nomina']."'";
			$resultado_mov=mysqli_query($conexion, $consulta_mov);
		
			if(num_rows($resultado_mov)==0)
			{
				$consulta="select * from nomconceptos where codcon='".$concepto[$valor]."'";
				$resultado_con=mysqli_query($conexion, $consulta);
				$fila=fetch_array($resultado_con);
				$REF=$ref[$valor];
				//echo $fila['formula'],"<br>";
				eval($fila['formula']);
				//echo $SUELDO," - ",$HORABASE," + ",$REF;
				
				if($MONTO<=0 && $fila['montocero']==1)
				{
					$entrar=0;
				}
				else
				{
					$entrar=1;
				}
				//echo $entrar,"<br>";
				if($entrar==1)
				{
				$consulta="insert into nom_movimientos_nomina "
                                        . "(codnom, "
                                        . "codcon,"
                                        . "ficha,"
                                        . "impdet,"
                                        . "mes,"
                                        . "anio,"
                                        . "tipcon,"
                                        . "valor,"
                                        . "monto,"
                                        . "cedula,"
                                        . "unidad,"
                                        . "descrip,"
                                        . "codnivel1,"
                                        . "codnivel2,"
                                        . "codnivel3,"
                                        . "codnivel4,"
                                        . "codnivel5,"
                                        . "codnivel6,"
                                        . "codnivel7,"
                                        . "tipnom,"
                                        . "contractual,"
                                        . "suesal,"
                                        . "cod_cargo) "
                                        . "values "
                                        . "('".$_POST['nomina']."',"
                                        . " '".$concepto[$valor]."',"
                                        . "'".$_POST['ficha']."',"
                                        . "'$fila[impdet]',"
                                        . "'".$fila_nom['mes']."',"
                                        . "'".$fila_nom['anio']."',"
                                        . "'".$tipoconcepto[$valor]."',"
                                        . "'".$REF."',"
                                        . "'".$MONTO."',"
                                        . "'$CEDULA',"
                                        . "'".$unidad[$valor]."',"
                                        . "'".$descripcion[$valor]."',"
                                        . "'$NIVEL1',"
                                        . "'$NIVEL2',"
                                        . "'$NIVEL3',"
                                        . "'$NIVEL4',"
                                        . "'$NIVEL5',"
                                        . "'$NIVEL6',"
                                        . "'$NIVEL7',"
                                        . "'".$_SESSION['codigo_nomina']."',"
                                        . "'$fila[contractual]',"
                                        . "'$SUELDO',"
                                        . "'$CODCARGO')";
				if(!$resultado=mysqli_query($conexion, $consulta))
				{
					echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
					alert('No se puede calcular conceptos a esta persona')
					</SCRIPT>";
				}
				}
			}
		}
                $sql_log = "INSERT INTO log_transacciones 
                (cod_log, 
                descripcion, 
                fecha_hora, 
                modulo, 
                url, 
                accion, 
                valor, 
                usuario,
                host) 
                VALUES 
                (NULL, 
                '".$descripcion_log."', "
                . "now(), "
            . "'".$modulo."', "
            . "'".$url."', "
            . "'".$accion."',"
            . "'".$cod."',"
            . "'".$_SESSION['usuario'] ."',"
            . "'".$host_ip."')";

            $res_log = mysqli_query($conexion, $sql_log) or die("no se actualizo el Tipo de personal");    
	}
        
	else
	{
		echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
		alert('No se puede calcular conceptos a esta persona')
		</SCRIPT>";
	}
	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	window.opener.document.forms[0].buscar.value=$_POST[ficha]
	window.opener.document.forms[0].submit()
	window.close()
	</SCRIPT>";
	
}
//DECLARACION DE LIBRERIAS





if(isset($_POST['buscar'])){
	
$consulta = "select * from ".$tabla." where"."  descrip  LIKE '%".$_POST['buscar']."%' OR codcon = '".$_POST['buscar']."' AND";
$longitud = strlen($consulta);
    $consulta = substr($consulta, 0, $longitud - 4);


	
}else{
$consulta="select nomconceptos.codcon as codcon,nomconceptos.descrip as descrip,nomconceptos.tipcon as tipcon,nomconceptos.unidad as unidad,nomconceptos.formula as formula, nomconceptos.modifdef as modifdef from nomconceptos join nomconceptos_frecuencias on(nomconceptos_frecuencias.codcon=nomconceptos.codcon) join nomconceptos_tiponomina on(nomconceptos_tiponomina.codcon=nomconceptos.codcon) join nom_nominas_pago on(nomconceptos_tiponomina.codtip=nom_nominas_pago.codtip) where 
nomconceptos_tiponomina.codtip='".$_SESSION['codigo_nomina']."' and nomconceptos_frecuencias.codfre='".$fila_nom['frecuencia']."' and nomconceptos.contractual='0' group by nomconceptos.tipcon, nomconceptos.codcon, nomconceptos.descrip order by nomconceptos.codcon,nomconceptos.tipcon";
}

//echo $consulta;

//echo $consulta." este es el valor quemuestra ";
$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);


?>
<script language="JavaScript" type="text/javascript">

function enviar(op){
	
	document.frmPrincipal.opcion.value=op;
	document.frmPrincipal.submit();
}
</script>

<FORM name="frmPrincipal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo $modulo; ?><?php echo " / Nombre ".$_GET['nombre']." 
							 - Nº: ".$_GET['ficha']." - Cedula: ".$_GET['cedula'] ?>
							</div>
							<div class="actions">
								<?php boton_metronic('ok',"MarcarTodos('seleccion[]');",2,'Marcar o Desmarcar Todos','checkTodos'); ?>
								<?php boton_metronic('back',"enviar('guardar');",2,'Aceptar'); ?>

								<?php boton_metronic('cancel',"window.close();",2); ?>
							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-2">
										<div class="input-group">
											<input input type="text" name="buscar" class="form-control" id="buscar" placeholder="Buscar">
											<span class="input-group-addon">
											<i class="fa fa-search"></i>
										</span>	
									</div>
								</div>
								<div class="col-md-1">
									<? boton_metronic('search','frmPrincipal',1); ?>
								</div>								
								<div class="col-md-1">
									<? boton_metronic('show_all',$url."?pagina=".$pagina."&ficha=".$ficha."&nomina=".$nombre_nomina); ?>
								</div>
							</div>
                                                    
                                                        <div class="row">
								<div class="col-md-6">
									<div class="input-group">
										<select name="codnivel1" id="codnivel1" class="form-control select2me">
                                                                                        <?php
                                                                                                
                                                                                                while($fila_nivel=fetch_array($resultado_nivel))
                                                                                                {
                                                                                                        if($NIVEL1==$fila_nivel['codorg'])
                                                                                                                echo "<option value='".$fila_nivel['codorg']."' selected>".$fila_nivel['descrip']."</option>";
                                                                                                        else
                                                                                                                echo "<option value='".$fila_nivel['codorg']."'>".$fila_nivel['descrip']."</option>";
                                                                                                }
                                                                                        ?>
                                                                                </select>
									</div>
								</div>
								
							</div>
							<div class="row">
								<div class="col-md-12">
									<table class="table table-condensed">
										<tbody>
										<tr>
											<td></td>
											<?
											foreach($titulos as $nombre)
											{
												echo "<td><STRONG>$nombre</STRONG></td>";
											}
											?>
										</tr>
										<? 
										if($num_paginas!=0)
										{
											$i=0; 
											while($fila=fetch_array($resultado))
											{
												$i++;
												if($i%2==0)
												{
													?>
													<tr class="tb-fila">
													<?
												}
												else
												{
													echo "<tr>";
												}
												?>
												<td><INPUT type="checkbox" name="seleccion[]" value="<?echo ($i-1)?>"></td>
												<?
												foreach($indices as $campo)
												{
													$nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;

													$var=$fila[$nom_tabla];
													if($nom_tabla=="tipcon")
													{
														echo "<td><input name=\"unidad[]\" type=\"hidden\" value=\"".$fila['unidad']."\"><input name=\"tipcon[]\" type=\"hidden\" value=\"$var\">$var</td>";		

													}
													elseif($nom_tabla=="descrip")
													{
														if($fila['modifdef']==1)
														{
															echo "<td><INPUT size=\"50\" type=\"text\" name=\"descripcion[]\" value=\"$var\"></td>";
														}
														else
														{
															echo "<td><INPUT size=\"50\" type=\"hidden\" name=\"descripcion[]\" value=\"$var\">$var</td>";
														}

													}
													elseif($nom_tabla=="codcon")
													{
														echo "<td><input name=\"codcon[]\" type=\"hidden\" value=\"$var\">$var</td>";	
													}
													else
													{
														echo "<td>$var</td>";
													}

												}
												echo "<td align=\"right\"><INPUT type=\"text\" name=\"referencia[]\" value=\"0\" class=\"form-control\"></td>";
												echo "</tr>";
											}
										}
										else
										{
											echo "<tr colspan=\"5\"><td>No existen registro con la busqueda especificada</td></tr>";
										}

										?>
										</tbody>
									</table>
								</div>
							</div>
                                                        
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?
//titulo_mejorada($modulo,"21.png","btn();|btn('back',\"enviar('guardar')\",2,'Aceptar');|btn('cancel','window.close()',2);","");
	
?>
<input name="opcion" id="opcion" type="hidden" value="">
<input name="marcar_todos" type="hidden" value="1">	
<input name="ficha" type="hidden" value="<?echo $ficha?>">	
<input name="nomina" type="hidden" value="<?echo $nombre_nomina?>">
<!--<input name="retorno" type="hidden" value="<?echo $retorno?>">		-->
<script type="text/javascript">
$(document).ready(function(){
	$("#checkTodos").on('click',function () {
		$("input:checkbox").prop('checked', $(this).prop("checked"));
		$.uniform.update();
	});
});

</script>
<?
//<input name=\"formula[]\" type=\"hidden\" value='".$fila['formula']."'>
pie_pagina_bootstrap($url,$pagina,"&tipo=".$tipob."&des=".$des."&ficha=".$ficha."&nomina=".$nombre_nomina,$num_paginas);
?>
</FORM>
</BODY>
</html>
<?cerrar_conexion($conexion);
include ("../footer4.php");
?>
