<div class="row tiempo-validate margen-inferior">
	<label class="col-md-5 control-label" for="posicion_tiempo"> Ubicado en columna:</label>
	<div class="col-md-2">
		<select class="form-control input-xsmall select2me" name="posicion_tiempo" id="posicion_tiempo">
			<option value='' class="text-center">-</option>
			<?php
				for ($i=1; $i < 21 ; $i++) 
				{ 
					if( isset($posicion_tiempo) && $posicion_tiempo == $i )
						echo "<option value='$i' selected>$i</option>";			
					else
						echo "<option value='$i'>$i</option>";	
				}
			?>
		</select>													
	</div>
</div>	
<div class="row tiempo-validate">
	<label class="col-md-5 control-label" for="formato_tiempo"> Formato de fecha y hora:</label>
	<div class="col-md-6">	
		<?php $array_formatos = array('m/d/Y h:i:s a' => 'MM/dd/yyyy hh:mm:ss a.m.', 
									  'Y-m-d H:i:s'   => 'yyyy-MM-dd hh:mm:ss'
									 ); 
		?>
		<select class="form-control select2me" name="formato_tiempo" id="formato_tiempo">
			<option value="">Seleccione un formato</option>
			<?php
				foreach ($array_formatos as $clave => $valor) 
				{
					if( isset($formato_tiempo) && $formato_tiempo == $clave )
						echo "<option value='$clave' selected>$valor</option>";			
					else
						echo "<option value='$clave'>$valor</option>";	
				}
			?>
		</select>											
	</div>
</div>
<script>      
$("#posicion_tiempo, #formato_tiempo").select2();
</script>			