<div class="form-group">
	<div class="tiempo-validate">
		<label class="col-md-2 control-label" for="posicion_hora" style="padding-left: 40px"> Hora ubicado en columna:</label>		
		<div class="col-md-2">
				<select class="form-control input-xsmall select2me" name="posicion_hora" id="posicion_hora">
					<option value='' class="text-center">-</option>
					<?php
						for ($i=1; $i < 21 ; $i++) 
						{ 
							if( isset($posicion_hora) && $posicion_hora == $i )
								echo "<option value='$i' selected>$i</option>";			
							else
								echo "<option value='$i'>$i</option>";	
						}
					?>
				</select>					
		</div>
	</div>	
	<label class="col-md-1 control-label" for="formato_hora"> Formato:</label>
	<div class="col-md-2" style="padding-right: 2px;">
			<?php $array_formatos = array('H' => 'hh', 
										  'G' => 'h'
										 ); 
			?>
			<select class="form-control input-small select2me" name="formato_hora" id="formato_hora">
				<?php
					foreach ($array_formatos as $clave => $valor) 
					{
						if( isset($formato_hora) && $formato_hora == $clave )
							echo "<option value='$clave' selected>$valor</option>";			
						else
							echo "<option value='$clave'>$valor</option>";	
					}
				?>
			</select>					
	</div>											
	<label class="col-md-3 control-label" for="posicion_indicador" style="text-align: right"> Indicador a.m./p.m. ubicado en columna:</label>
	<div class="col-md-2">		
			<select class="form-control select2me" name="posicion_indicador" id="posicion_indicador">
				<option value='' class="text-center">-</option>
				<?php
					for ($i=1; $i < 21 ; $i++) 
					{ 
						if( isset($posicion_indicador) && $posicion_indicador == $i )
							echo "<option value='$i' selected>$i</option>";			
						else
							echo "<option value='$i'>$i</option>";	
					}
				?>
			</select>				
	</div>
</div>
<div class="form-group">
	<div class="tiempo-validate">
		<label class="col-md-2 control-label" for="posicion_minutos" style="padding-left: 40px"> Minutos ubicado en columna:</label>	
		<div class="col-md-2">
				<select class="form-control input-xsmall select2me" name="posicion_minutos" id="posicion_minutos">
					<option value='' class="text-center">-</option>
					<?php
						for ($i=1; $i < 21 ; $i++) 
						{ 
							if( isset($posicion_minutos) && $posicion_minutos == $i )
								echo "<option value='$i' selected>$i</option>";			
							else
								echo "<option value='$i'>$i</option>";	
						}
					?>
				</select>					
		</div>	
	</div>
	<label class="col-md-1 control-label" for="formato_minutos"> Formato:</label>
	<div class="col-md-2" style="padding-right: 2px;">
			<?php $array_formatos = array('i' => 'mm'); ?>
			<select class="form-control input-small select2me" name="formato_minutos" id="formato_minutos">
				<option value='i'>mm</option>
				<?php
					foreach ($array_formatos as $clave => $valor) 
					{
						if( isset($formato_minutos) && $formato_minutos == $clave )
							echo "<option value='$clave' selected>$valor</option>";			
						else
							echo "<option value='$clave'>$valor</option>";	
					}
				?>
			</select>					
	</div>
	
	<label class="col-md-3 control-label" for="formato_indicador" style="text-align: right"> Formato indicador a.m./p.m.:</label>
	<div class="col-md-2">
		<?php $array_formatos = array('a' => 'a.m./p.m.'); 
		?>
			<select class="form-control select2me" name="formato_indicador" id="formato_indicador">
				<option value=""> - </option>
				<?php
					foreach ($array_formatos as $clave => $valor) 
					{
						if( isset($formato_indicador) && $formato_indicador == $clave )
							echo "<option value='$clave' selected>$valor</option>";			
						else
							echo "<option value='$clave'>$valor</option>";	
					}
				?>
			</select>					
	</div>
</div>
<script>
$("#posicion_hora, #formato_hora, #posicion_indicador, #posicion_minutos, #formato_minutos, #formato_indicador").select2();
</script>
