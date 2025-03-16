<?php
/*
Modificado por Hiram Loreto para MUPA*/

session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion        = new bd($_SESSION['bd']);
$sql             = "SELECT codorg,descrip FROM nomprofesiones";
$res_profesiones = $conexion->query($sql, "utf8");

$sql             = "SELECT * FROM nominstruccion";
$res_instruccion = $conexion->query($sql, "utf8");

$sql             = "SELECT * FROM nomdesempeno";
$res_desempeno   = $conexion->query($sql, "utf8");
include("../../includes/dependencias.php"); 
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
						<i class="fa fa-folder-open-o" aria-hidden="true"></i> Informacion Personal
					</div>

	            	<div class="actions">
						<a class="btn btn-sm blue"  onclick="javascript: window.location='elegibles_list.php'">
							<i class="fa fa-arrow-left"></i>
							Atras
						</a>
					</div>
	            </div>
	            <div class="portlet-body">

	            	<div class="row">
            			<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
            				<div class="row">
            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label>Cédula</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <input type="text" name="cedula" class="form-control" placeholder="Cédula" required>
            				    </div>

            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label>Nacionalidad</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <input type="text" name="nacionalidad" maxlength="60" class="form-control" placeholder="Nacionalidad" required>
            				    </div>
            				</div>
            				<br><div class="row">
            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label>Nº Seguro Social</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <input type="text" name="seguro_social" class="form-control" placeholder="Seguro Social" required>
            				    </div>

            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label>Lugar de Nacimiento</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <input type="text" name="lugarnac" maxlength="60" class="form-control" placeholder="Lugar de Naciemiento" required>
            				    </div>
            				</div>
            				<br>
            				<div class="row">
            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label> Primer Nombre</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <input type="text" name="nombres" class="form-control" placeholder="Primer Nombre" required>
            				    </div>

            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
            				        <label>Fecha Nacimiento</label>
            				    </div>
            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            				        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10" required>
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
            				    </div>
            				</div>
	            		</div>
	            		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	            			<img src="fotos/silueta.gif" lowsrc="fotos/silueta.gif" border="1" name="imgFoto" id="imgFoto" width="200" height="200" align="middle" class="img-thumbnail pull-right" style="top:0px;padding-bottom: 10x;">
	            		
	            			
	            		</div>

	            	</div>

	            	<div class="row">

		            		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Segundo Nombre</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="nombres2" maxlength="60" class="form-control" placeholder="Segundo Nombre">
	            				    </div>
	            				    <?php
										$data = array('Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Unido');
									?>

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Estado Civil</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <select name="estado_civil" id="estado_civil" class="form-control select2">
											<?php
												
												foreach ($data as $estado_civil) 
												{
													echo "<option value='".$estado_civil."'>".$estado_civil."</option>";
												}
											?>
										</select>
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Apellido Paterno</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="apellidos" class="form-control" placeholder="Apellido paterno" required>
	            				    </div>
	            				    

	            				   <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Sexo</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <select name="sexo" id="sexo" class="form-control">
	            				        	<option value="Masculino">Masculino</option>
	            				        	<option value="Femenino">Femenino</option>
	            				        </select>
	            				    </div>
	            				</div>
	            				<br>
	            				
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Apellido Materno</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="apellido2" class="form-control" placeholder="Apellido Materno">
	            				    </div>
	            				    

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Tipo de Sangre</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <select name="tip_sang" id="tip_sang" class="form-control">
	            				        	
	            				        </select>
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>Apellido Casada</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="apellido3" class="form-control" placeholder="Apellido Casada">
	            				    </div>
	            				    

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				    </div>
	            				</div>
	            				<br>
	            				<div class="row">
	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				        <label>En caso de Emergencia, Contactar a</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            				        <input type="text" name="contacto" class="form-control" placeholder="Contacto Emergencia" required>
	            				    </div>
	            				    

	            				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
	            				    	<label>Al teléfono</label>
	            				    </div>
	            				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

		            				    <input type="text" name="num_contacto" class="form-control" placeholder="Contacto Emergencia" required>
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
		            			<input class="form-control"  type="checkbox" name="_incapacidad" id="_incapacidad">
		            		</div>
		            		<span id="tiene_discapacidad">
		            			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
		            			<label>¿Cuál?</label>
			            		</div>
			            		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            			<input  class="form-control" type="text" name="incapacidad_esp">
			            		</div>
		            		</span>
		            		
		            	</div>
		            	<br>
		            	<div class="row">
		            		<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
		            			<label>Alergias y Afecciones</label>
		            		</div>
		            		<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
		            			<input class="form-control"  type="text" name="alergias_afecciones">
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
        				        	echo '<option value="'.$profesion['codorg'].'">'.$profesion['descrip'].'</option>';
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
        				        	echo '<option value="'.$instruccion['codigo'].'">'.$instruccion['descripcion'].'</option>';
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
        				        	echo '<option value="'.$desempeno['codigo'].'">'.$desempeno['descripcion'].'</option>';
        				        }?>
        				        </select>
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Años de Experiencia</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="number" min="0" max="100" name="anios_exp" class="form-control" placeholder="Años de Experiencia" required>
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
        				        <input type="text" name="telefono" class="form-control" placeholder="Teléfono" required>
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Correo Electrónico</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="email" name="email" class="form-control" placeholder="Correo Electrónico" required>
        				    </div>
        				</div>
        				<br>
                    	<div class="row">
        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Dirección</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="text" name="direccion" class="form-control" placeholder="Dirección completa" required>
        				    </div>

        				    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
        				        <label>Observaciones</label>
        				    </div>
        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
        				        <input type="text" name="observacion" class="form-control" placeholder="Observaciones" required>
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
	        				        <input type="text" name="nombre_padre" class="form-control" placeholder="Nombre Completo del Padre">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Nombre Completo de la madre</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="nombre_madre" class="form-control" placeholder="Nombre Completo de la madre">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>Nombre del Cónyugue</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="nombre_conyugue" class="form-control" placeholder="Nombre del Cónyugue">
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
	        				        <input type="text" name="nombre_dep1" class="form-control" placeholder="Nombre del Dependiente 1">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco1" class="form-control" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac1" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control" type="text" name="discapacidad1"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep2" class="form-control" placeholder="Nombre del Dependiente 2">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco2" class="form-control" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac2" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control" type="text" name="discapacidad2"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep3" class="form-control" placeholder="Nombre del Dependiente 3">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco3" class="form-control" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac3" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control" type="text" name="discapacidad3"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep4" class="form-control" placeholder="Nombre del Dependiente 4">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco4" class="form-control" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac4" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control" type="text" name="discapacidad4"  placeholder="Indique discapacidad">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="nombre_dep5" class="form-control" placeholder="Nombre del Dependiente 5">
	        				    </div>
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <input type="text" name="parentesco5" class="form-control" placeholder="Parentesco">
	        				    </div>
	        				    
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				    	<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                        <input name="fecnac5" type="text"  class="form-control" placeholder="Inserte fecha" id="fechainicio" value="" maxlength="10">
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                            	<i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                	</div>
			            		</div>
	        				    	
	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
			            			<input  class="form-control" type="text" name="discapacidad5"  placeholder="Indique discapacidad">
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
	        				        <input type="text" name="telefono_ext" class="form-control" placeholder="Teléfono / Extensión">
	        				    </div>
	        				</div>
	        				<br>
	                    	<div class="row">

	        				    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
	        				        <label>E-Mail (institucional)</label>
	        				    </div>
	        				    <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
	        				        <input type="text" name="email_institucional" class="form-control" placeholder="Teléfono / Extensión">
	        				    </div>
	        				</div>
	        				<br>
			            	
			            </div>
			        </div>
	            	<hr>

		            <div class="portlet box blue">
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
	        				        <input type="text" name="referencia1" class="form-control" placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia1" class="form-control" placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia1"  placeholder="E-mail">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="referencia2" class="form-control" placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia2" class="form-control" placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia2"  placeholder="E-mail">
			            		</div>
	        				</div>
	        				<br>
	                    	<div class="row">
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="referencia3" class="form-control" placeholder="Nombres y Apellidos">
	        				    </div>
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	        				        <input type="text" name="tel_referencia3" class="form-control" placeholder="Teléfono">
	        				    </div>
	        				    
	        				    	
	        				    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
			            			<input  class="form-control" type="text" name="email_referencia3"  placeholder="E-mail">
			            		</div>
	        				</div>
			            	
			            </div>
			        </div>
	            	<hr>
	                
	                
                    <div align="center">
                        <input class="btn btn-primary" type="submit" id="importar" name="importar" value="Aceptar">&nbsp;
                        <input type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript: window.location='elegibles_list.php'">
                    </div>
	            </div>
	        </div>
	    </div>
	</div>
	</form>
</div>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		$("#tiene_discapacidad").hide();
		$("#tiene_discapacidad_dep").hide();
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
