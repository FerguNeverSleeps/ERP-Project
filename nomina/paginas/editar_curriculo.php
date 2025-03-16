<?php
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=new bd($_SESSION['bd']);
$cedula  = $_GET['cedula'];
$sql     = "SELECT A.*, B.descrip, C.descripcion, D.descripcion
			FROM nomelegibles AS A 
	        LEFT JOIN nomprofesiones AS B ON A.cod_profesion = B.codorg
	        LEFT JOIN nominstruccion AS C ON A.grado_instruccion = C.codigo 
	        LEFT JOIN nomdesempeno AS D ON A.area_desempeno = D.codigo
			WHERE A.cedula = '{$cedula}'
			";
$res     = $conexion->query($sql, "utf8");
$persona = $res->fetch_assoc();

$sql             = "SELECT codorg,descrip FROM nomprofesiones";
$res_profesiones = $conexion->query($sql, "utf8");

$sql             = "SELECT * FROM nominstruccion";
$res_instruccion = $conexion->query($sql, "utf8");

$sql             = "SELECT * FROM nomdesempeno";
$res_desempeno   = $conexion->query($sql, "utf8");
include("../../includes/dependencias.php");
function fecha_formato($fecha)
{
	if ( $fecha == "0000-00-00")
	{
		$fecha=date("d-m-Y");
	}else{
		$e = explode('-', $fecha);
		$fecha = $e[2]."-".$e[1]."-".$e[0];
	}
	return $fecha;
}
?>

<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .text-middle{
  	vertical-align: middle !important
  }

  .ajustar-texto
  {
  	white-space: normal !important;
  }

  td.icono
  {
  	padding-left: 0px !important;
  	padding-right: 0px !important;
  	width: 10px !important;
  }
</style>
<script type="text/javascript" src="../lib/common.js"></script>
<script type="text/javascript">
function GenerarNomina()
{
	AbrirVentana('barraprogreso_1.php', 150, 500, 0);
}

function CerrarVentana()
{
	javascript:window.close();
}

function showProcesando(){
	//console.log("Mostrar Procesando");
    App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
    });
}

</script>
<div class="container">
	<form action="importar_curriculo.php" method="post" enctype="multipart/form-data">
	<div class="row">
	    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	        <div class="portlet box blue">
	            <div class="portlet-title">
	            	<div class="caption">
						<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Cargar Curriculo
					</div>

	            	<div class="actions">
						<a class="btn btn-sm blue"  onclick="javascript: window.location='elegibles_list.php'">
							<i class="fa fa-plus"></i>
							Atras
						</a>
					</div>
	            </div>
	            <div class="portlet-body">
	            	<div class="row">
	            		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            			<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">		            				
	            				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            					<?php if (file_exists($persona['foto']) ): ?>
            						<img src="<?php echo $persona['foto']; ?>" lowsrc="fotos/silueta.gif" border="1" name="imgFoto" id="imgFoto" width="200" height="200" align="middle" class="img-thumbnail pull-right" style="top:0px;padding-bottom: 10x;">
            					<?php else: ?>

	            					<img src="fotos/silueta.gif" lowsrc="fotos/silueta.gif" border="1" name="imgFoto" id="imgFoto" width="200" height="200" align="middle" class="img-thumbnail pull-right" style="top:0px;padding-bottom: 10x;">
	            				<?php endif ?>
            				    </div>
	            			</div>
	            			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Nombre</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				    <input type="hidden" name="accion" value="edit">
	            				    <input type="hidden" name="cedula_ant" value="<?php echo $_GET['cedula']; ?>">
	            				        <input type="text" name="nombres" class="form-control" value="<?php echo $persona['nombres'] ?>" required>
	            				    </div>

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Apellido</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="apellidos" class="form-control" value="<?php echo $persona['apellidos'] ?>" required>
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Cedula</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="cedula" class="form-control" value="<?php echo $persona['cedula'] ?>" required>
	            				    </div>

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Sexo</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <select name="sexo" id="sexo" class="form-control">
	            				        	<option value="<?php echo $persona['sexo'] ?>"><?php echo $persona['sexo'] ?></option>
	            				        	<option value="Masculino">Masculino</option>
	            				        	<option value="Femenino">Femenino</option>
	            				        </select>
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Fecha de Nacimiento</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                            <input name="fecnac" id="fecnac" type="text"  class="form-control" value="<?php echo fecha_formato($persona['fecnac']) ?>" maxlength="10" required>
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                	<i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                    	</div>
	            				    </div>

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Lugar de Vacimiento</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="lugarnac" class="form-control" value="<?php echo $persona['fecnac'] ?>" required>
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Cargar Foto</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="file" name="foto" id="foto">
	            				    </div>

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Adjuntar Documento</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="file" name="archivo" id="archivo">
	            				    </div>
	            				</div>
	            			</div>
	            		</div>
	            	</div><hr>
	            <div class="portlet box blue">
		            <div class="portlet-title">
		            	<div class="caption">
							<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Discapacidad, alergia y afectaciones
						</div>
		            </div>
		            <div class="portlet-body">
		            	<div class="row">
		            		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		            			<label>¿Tiene alguna incapacidad?</label>
		            		</div>
		            		<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
		            			<?php $discaa = ($persona['discapacidad'] == 1 ) ? 'checked' : '' ; ?>
		            			<input class="form-control"  type="checkbox" name="_incapacidad" id="_incapacidad" 
		            			<?php echo $discaa;?> />
		            		</div>
		            		<span id="tiene_discapacidad">
		            			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		            			<label>¿Cuál?</label>
			            		</div>
			            		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            			<input  class="form-control" type="text" name="incapacidad_esp"  value="<?php echo $persona['discapacidad_escifica'] ?>">
			            		</div>
		            		</span>
		            		
		            	</div>
		            	<br>
		            	<div class="row">
		            		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		            			<label>Alergias y Afecciones</label>
		            		</div>
		            		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
		            			<input class="form-control"  type="text" name="alergias_afecciones" value="<?php echo $persona['alergias_afecciones'] ?>">
		            		</div>
		            		
		            		
		            	</div>
		            	
		            </div>
		        </div><hr>
		        <div class="portlet box blue">
		            <div class="portlet-title">
		            	<div class="caption">
							<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> <strong>Informacion Academica:</strong>
						</div>
		            </div>
		            <div class="portlet-body">
		            	<div class="row">
        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Profesión</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <select name="cod_profesion" id="cod_profesion" class="form-control" required>
        				        	<option>Seleccione</option>
        				        <?php while ($profesion = $res_profesiones->fetch_assoc()){
        				        	if ($profesion['codorg'] == $persona['cod_profesion']) {
        				        		echo '<option value="'.$profesion['codorg'].'" selected>'.$profesion['descrip'].'</option>';
        				        	}
        				        	else
        				        	{
        				        		echo '<option value="'.$profesion['codorg'].'">'.$profesion['descrip'].'</option>';
        				        	}
        				        }?>
        				        </select>
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Grado de Instruccion</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <select name="grado_instruccion" id="grado_instruccion" class="form-control" required>
        				        	<option>Seleccione</option>
        				        <?php while ($instruccion = $res_instruccion->fetch_assoc()){
        				        	if ($instruccion['codigo'] == $persona['grado_instruccion']) {
        				        		echo '<option value="'.$instruccion['codigo'].'" selected>'.$instruccion['descripcion'].'</option>';
        				        	}
        				        	else
        				        	{
        				        		echo '<option value="'.$instruccion['codigo'].'">'.$instruccion['descripcion'].'</option>';
        				        	}
        				        }?>
        				        </select>
        				    </div>
        				</div>
        				<br>
        				<div class="row">
        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Area de Desempeño</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <select name="area_desempeno" id="area_desempeno" class="form-control">
        				        	<option>Seleccione</option>
        				        <?php while ($desempeno = $res_desempeno->fetch_assoc()){
        				        	if ($desempeno['codorg'] == $persona['area_desempeno']) {
        				        		echo '<option value="'.$desempeno['codigo'].'">'.$desempeno['descripcion'].'</option>';
        				        	}
        				        	else
        				        	{
        				        		echo '<option value="'.$desempeno['codigo'].'">'.$desempeno['descripcion'].'</option>';
        				        	}
        				        }?>
        				        </select>
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Años de Experiencia</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="number" min="0" max="100" name="anios_exp" class="form-control" placeholder="Años de Experiencia" value="<?php echo $persona['anios_exp'] ?>">
        				    </div>
        				</div>
		            </div>
		        </div>
		        <hr>
	            <div class="portlet box blue">
		            <div class="portlet-title">
		            	<div class="caption">
							<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Infomacion de Contacto
						</div>
		            </div>
		            <div class="portlet-body">
		            	<div class="row">
        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Teléfono</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="text" name="telefono" class="form-control" placeholder="Teléfono"  value="<?php echo $persona['telefono'] ?>">
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Correo Electrónico</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="email" name="email" class="form-control" placeholder="Correo Electrónico"  value="<?php echo $persona['email'] ?>">
        				    </div>
        				</div>
        				<br>
                    	<div class="row">
        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Dirección</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="text" name="direccion" class="form-control" placeholder="Dirección completa"  value="<?php echo $persona['direccion'] ?>">
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Observaciones</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="text" name="observacion" class="form-control" placeholder="Observaciones"  value="<?php echo $persona['observacion'] ?>">
        				    </div>
        				</div>
		            	
		            </div>
		        </div>
	            	<hr>
		            <div class="portlet box blue">
			            <div class="portlet-title">
			            	<div class="caption">
								<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Infomacion Adicional
							</div>
			            </div>
			            <div class="portlet-body">
			            	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Nombre Completo del Padre</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="nombre_padre" class="form-control"  value="<?php echo $persona['nombre_padre'] ?>" placeholder="Nombre Completo del Padre">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Nombre Completo de la madre</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="nombre_madre" class="form-control"  value="<?php echo $persona['nombre_madre'] ?>" placeholder="Nombre Completo de la madre">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Nombre del Cónyugue</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="nombre_conyugue" class="form-control"  value="<?php echo $persona['nombre_conyugue'] ?>" placeholder="Nombre del Cónyugue">
	        				    </div>
	        				</div>
	        				<br>
			            	
			            </div>
			        </div>
	            	<hr>

		            <div class="portlet box blue">
			            <div class="portlet-title">
			            	<div class="caption">
								<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Infomacion de Dependientes
							</div>
			            </div>
			            <div class="portlet-body">
			            	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label><b>Nombre del Dependiente</b></label>
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<label><b>Parentesco</b></label>
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label><b>Fecha de Nacimiento</b></label>
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		            			<label><b>¿Tiene alguna incapacidad?</b></label>
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep1" class="form-control"  value="<?php echo $persona['nombre_dep1'] ?>" placeholder="Nombre del Dependiente 1">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco1" class="form-control"  value="<?php echo $persona['parentesco1'] ?>" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac1" type="text"  class="form-control"  value="<?php echo $persona['fecnac1'] ?>" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control"  value="<?php echo $persona['discapacidad1'] ?>" type="text" name="discapacidad1"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep2" class="form-control"  value="<?php echo $persona['nombre_dep2'] ?>" placeholder="Nombre del Dependiente 2">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco2" class="form-control"  value="<?php echo $persona['parentesco2'] ?>" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac2" type="text"  class="form-control"  value="<?php echo $persona['fecnac2'] ?>" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control"  value="<?php echo $persona['discapacidad2'] ?>" type="text" name="discapacidad2"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep3" class="form-control"  value="<?php echo $persona['nombre_dep3'] ?>" placeholder="Nombre del Dependiente 3">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco3" class="form-control"  value="<?php echo $persona['parentesco3'] ?>" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac3" type="text"  class="form-control"  value="<?php echo $persona['fecnac3'] ?>" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control"  value="<?php echo $persona['discapacidad3'] ?>" type="text" name="discapacidad3"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep4" class="form-control"  value="<?php echo $persona['nombre_dep4'] ?>" placeholder="Nombre del Dependiente 4">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco4" class="form-control"  value="<?php echo $persona['parentesco4'] ?>" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac4" type="text"  class="form-control"  value="<?php echo $persona['fecnac4'] ?>" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control"  value="<?php echo $persona['discapacidad4'] ?>" type="text" name="discapacidad4"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep5" class="form-control"  value="<?php echo $persona['nombre_dep5'] ?>" placeholder="Nombre del Dependiente 5">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco5" class="form-control"  value="<?php echo $persona['parentesco5'] ?>" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac5" type="text"  class="form-control"  value="<?php echo $persona['fecnac5'] ?>" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control"  value="<?php echo $persona['discapacidad5'] ?>" type="text" name="discapacidad5"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
			            	
			            </div>
			        </div>
			        <hr>
		            <div class="portlet box blue">
			            <div class="portlet-title">
			            	<div class="caption">
								<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Infomacion de Contacto Laboral
							</div>
			            </div>
			            <div class="portlet-body">
			            	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Teléfono / Extensión</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="telefono_ext" class="form-control"  value="<?php echo $persona['telefono_ext'] ?>" placeholder="Teléfono / Extensión">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>E-Mail (institucional)</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="email_institucional" class="form-control"  value="<?php echo $persona['email_institucional'] ?>" placeholder="Teléfono / Extensión">
	        				    </div>
	        				</div>
	        				<br>
			            	
			            </div>
			        </div>
	            	<hr><div class="portlet box blue">
			            <div class="portlet-title">
			            	<div class="caption">
								<i class="fa fa-folder-open-o fa-lg" aria-hidden="true"></i> Referencias
							</div>
			            </div>
			            <div class="portlet-body">
			            	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <label><b>Nombres y Apellidos</b></label>
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				    	<label><b>Teléfono</b></label>
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <label><b>E-mail</b></label>
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="referencia1" class="form-control" value="<?php echo $persona['referencia1'] ?>"  placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia1" class="form-control" value="<?php echo $persona['tel_referencia1'] ?>"  placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia1"  value="<?php echo $persona['email_referencia1'] ?>"  placeholder="E-mail">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="referencia2" class="form-control" value="<?php echo $persona['referencia2'] ?>"  placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia2" class="form-control" value="<?php echo $persona['tel_referencia2'] ?>"  placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia2"  value="<?php echo $persona['email_referencia2'] ?>"  placeholder="E-mail">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="referencia3" class="form-control" value="<?php echo $persona['referencia3'] ?>"  placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia3" class="form-control" value="<?php echo $persona['tel_referencia3'] ?>"  placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia3"  value="<?php echo $persona['email_referencia3'] ?>"  placeholder="E-mail">
			            		</div>
	        				</div>
			            	
			            </div>
			        </div>
	            	<hr>
                    <div align="right">
                        <input class="btn btn-primary" type="submit" id="importar" name="importar" value="Editar">&nbsp;
                        <input type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript: window.location='elegibles_list.php'">
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
	</form>
</div>
<?php include("../footer4.php"); ?>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		$("#_incapacidad").on("click",function(){
			$("#_incapacidad").attr("checked") ? $("#tiene_discapacidad").show() : $("#tiene_discapacidad").hide();
		});
		$("#dep_incapacidad").on("click",function(){
			$("#dep_incapacidad").attr("checked") ? $("#tiene_discapacidad_dep").show() : $("#tiene_discapacidad_dep").hide();
		});
		$.get("ajax/obtenerTipoSangre.php",function(res){
			$("#tip_sang").empty();
			$("#tip_sang").append(res);


		});
		$.get("ajax/obtenerAreaDes.php",function(res){
			$("#area_desempeno").empty();
			$("#area_desempeno").append(res);


		});
        $("#codcargo").select2();

	});
</script>
</html>

</html>
