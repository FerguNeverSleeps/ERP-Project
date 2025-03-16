<div class="row">
	<!--/span-->
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label col-md-4">Puesto de Trabajo <span class="required">*</span></label>
			<div class="col-md-8">
				<?php 
					$sql  = "SELECT v.*, CONCAT_WS(', ', codigo, cliente, ubicacion, descripcion) as desc_puesto	
						     FROM   vig_puestos v";
					$res  = $db->query($sql);
				?>
				<select name="puesto_id" id="puesto_id" class="form-control select2" data-placeholder="Seleccione un puesto">
					<?php
						//if($operacion=='agregar')
						echo "<option value=''>Seleccione un puesto</option>";

						while($fila = $res->fetch_assoc())
						{
							if(isset($integrante->puesto_id) && $integrante->puesto_id==$fila['id_puesto'])
							{
								$registro_puesto = $fila;
								echo "<option value='".$fila['id_puesto']."' selected>".$fila['desc_puesto']."</option>";
							}
							else
								echo "<option value='".$fila['id_puesto']."'>".$fila['desc_puesto']."</option>";
						}
					?>
				</select>			
			</div>
		</div>
	</div>
	<!--/span-->
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label col-md-4 padding-left-40 padding-right-5">Turnos <span class="required">*</span></label>
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
</div>
<div class="row" id="horario_puesto" style="display: <?php echo (isset($integrante->puesto_id) && $integrante->puesto_id!='') ? 'block': 'none'; ?>">
	<label class="control-label col-md-2">Horario del Puesto</label>
	<div class="col-md-10">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">Lunes</th>
					<th class="text-center">Martes</th>
					<th class="text-center">Mi&eacute;rcoles</th>
					<th class="text-center">Jueves</th>
					<th class="text-center">Viernes</th>
					<th class="text-center">S&aacute;bado</th>
					<th class="text-center">Domingo</th>
				</tr>
			</thead>
			<tbody class="mostrarHorario">
				<tr>
					<?php
						if($registro_puesto)
						{
						?>
							<td class="text-center"><?php echo $registro_puesto['dia1_desde']; ?><br>-<br><?php echo $registro_puesto['dia1_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia2_desde']; ?><br>-<br><?php echo $registro_puesto['dia2_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia3_desde']; ?><br>-<br><?php echo $registro_puesto['dia3_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia4_desde']; ?><br>-<br><?php echo $registro_puesto['dia4_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia5_desde']; ?><br>-<br><?php echo $registro_puesto['dia5_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia6_desde']; ?><br>-<br><?php echo $registro_puesto['dia6_hasta']; ?></td>
							<td class="text-center"><?php echo $registro_puesto['dia7_desde']; ?><br>-<br><?php echo $registro_puesto['dia7_hasta']; ?></td>
						<?php
						}
					?>
				</tr>
			</tbody>
		</table>				
	</div>	
</div>