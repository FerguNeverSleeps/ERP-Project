<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label col-md-4">Turnos <span class="required">*</span></label>
			<div class="col-md-8">
				<?php 
					$sql = "SELECT turno_id, concat('Turno ', descripcion) as descripcion FROM nomturnos";
					$res = $db->query($sql);
				?>
				<select name="turno_id" id="turno_id" class="form-control select2" data-placeholder="Seleccione un turno">
					<?php
						//if($operacion=='agregar')
						echo "<option value=''>Seleccione un turno</option>";

						while($fila = $res->fetch_assoc())
						{
							if(isset($integrante->turno_id) && $integrante->turno_id==$fila['turno_id'])
								echo "<option value='".$fila['turno_id']."' selected>".$fila['descripcion']."</option>";
							else
								echo "<option value='".$fila['turno_id']."'>".$fila['descripcion']."</option>";
						}
					?>
				</select>
			</div>
		</div>
	</div>
	<!--/span-->
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label col-md-4 padding-left-40">Posici&oacute;n</label>
			<div class="col-md-8">
				<?php 
					$sql = "SELECT nomposicion_id, sueldo_propuesto FROM nomposicion";
					$res = $db->query($sql);

					$max_sueldo = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : 0;
				?>
				<select name="nomposicion_id" id="nomposicion_id" class="form-control select2" data-placeholder="Seleccione una posición">
					<?php
						//if($operacion=='agregar')
						echo "<option value=''>Seleccione una posición</option>";

						while($fila = $res->fetch_assoc())
						{
							if(isset($integrante->nomposicion_id) && $integrante->nomposicion_id==$fila['nomposicion_id'])
							{
								$max_sueldo = $fila['sueldo_propuesto'];
								echo "<option value='".$fila['nomposicion_id']."' selected>".$fila['nomposicion_id']."</option>";
							}
							else
								echo "<option value='".$fila['nomposicion_id']."'>".$fila['nomposicion_id']."</option>";
						}
					?>
				</select>	
				<input type="hidden" name="posicion_original" id="posicion_original" value="<?php echo isset($integrante->nomposicion_id) ? $integrante->nomposicion_id : ''; ?>">			
			</div>
		</div>
	</div>
</div>