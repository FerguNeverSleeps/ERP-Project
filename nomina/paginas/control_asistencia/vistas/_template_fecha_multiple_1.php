<div class="row margen-inferior">
	<div class="tiempo-validate">
		<label class="col-md-4 control-label" for="posicion_dia"> Día ubicado en columna:</label>
		<div class="col-md-3">
			<select class="form-control select2me" name="posicion_dia" id="posicion_dia">
				<option value='' class="text-center">-</option>
				<?php
					for ($i=1; $i < 21 ; $i++) 
					{ 
						if( isset($posicion_dia) && $posicion_dia == $i )
							echo "<option value='$i' selected>$i</option>";			
						else
							echo "<option value='$i'>$i</option>";	
					}
				?>
			</select>													
		</div>
	</div>
	<label class="col-md-2 control-label" for="formato_dia"> Formato:</label>
	<div class="col-md-3">	
		<?php $array_formatos = array('d' => 'dd', 
									  'j' => 'd'
									 ); 
		?>
		<select class="form-control select2me" name="formato_dia" id="formato_dia">
			<?php
				foreach ($array_formatos as $clave => $valor) 
				{
					if( isset($formato_dia) && $formato_dia == $clave )
						echo "<option value='$clave' selected>$valor</option>";			
					else
						echo "<option value='$clave'>$valor</option>";	
				}
			?>
		</select>											
	</div>											
</div>	
<div class="row margen-inferior">
	<div class="tiempo-validate">
		<label class="col-md-4 control-label" for="posicion_mes"> Mes ubicado en columna:</label>
		<div class="col-md-3">
			<select class="form-control select2me" name="posicion_mes" id="posicion_mes">
				<option value='' class="text-center">-</option>
				<?php
					for ($i=1; $i < 21 ; $i++) 
					{ 
						if( isset($posicion_mes) && $posicion_mes == $i )
							echo "<option value='$i' selected>$i</option>";			
						else
							echo "<option value='$i'>$i</option>";	
					}
				?>
			</select>													
		</div>
	</div>
	<label class="col-md-2 control-label" for="formato_mes"> Formato:</label>
	<div class="col-md-3">
		<?php $array_formatos = array('m' => 'MM', 
									  'n' => 'M'
									 ); 
		?>	
		<select class="form-control select2me" name="formato_mes" id="formato_mes">
			<?php
				foreach ($array_formatos as $clave => $valor) 
				{
					if( isset($formato_mes) && $formato_mes == $clave )
						echo "<option value='$clave' selected>$valor</option>";			
					else
						echo "<option value='$clave'>$valor</option>";	
				}
			?>
		</select>										
	</div>	
</div>	
<div class="row">
	<div class="tiempo-validate">
		<label class="col-md-4 control-label" for="posicion_anio"> Año ubicado en columna:</label>
		<div class="col-md-3">
			<select class="form-control select2me" name="posicion_anio" id="posicion_anio">
				<option value='' class="text-center">-</option>
				<?php
					for ($i=1; $i < 21 ; $i++) 
					{ 
						if( isset($posicion_anio) && $posicion_anio == $i )
							echo "<option value='$i' selected>$i</option>";			
						else
							echo "<option value='$i'>$i</option>";	
					}
				?>
			</select>													
		</div>
	</div>
	<label class="col-md-2 control-label" for="formato_anio"> Formato:</label>
	<div class="col-md-3">	
		<?php $array_formatos = array('Y' => 'yyyy', 
									  'y' => 'yy'
									 ); 
		?>
		<select class="form-control select2me" name="formato_anio" id="formato_anio">
			<?php
				foreach ($array_formatos as $clave => $valor) 
				{
					if( isset($formato_anio) && $formato_anio == $clave )
						echo "<option value='$clave' selected>$valor</option>";			
					else
						echo "<option value='$clave'>$valor</option>";	
				}
			?>
		</select>												
	</div>	
</div>	
<script>
$("#posicion_dia, #formato_dia, #posicion_mes, #formato_mes, #posicion_anio, #formato_anio").select2();
</script>			
