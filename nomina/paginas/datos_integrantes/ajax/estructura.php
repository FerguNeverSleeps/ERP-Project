<?php 
require_once('../../../lib/database.php');

$db           = new Database($_SESSION['bd']);

$ficha_actual = (isset($_GET['ficha'])) ? $_GET['ficha'] : '';
$operacion    = (isset($_GET['edit']))  ? 'editar' : 'agregar';


$sql = "SELECT e.nivel1, e.nomniv1, e.nivel2, e.nomniv2, e.nivel3, e.nomniv3, e.nivel4, e.nomniv4, e.nivel5, e.nomniv5,
		       e.nivel6, e.nomniv6, e.nivel7, e.nomniv7, e.tipo_empresa
		FROM nomempresa e";
$res        = $db->query($sql);
$empresa    = $res->fetch_object();
$sql = "SELECT * FROM nompersonal WHERE ficha='".$ficha_actual."'";
	$res = $db->query($sql);
	$integrante = $res->fetch_object();
	//echo $operacion,"<br>",$integrante->{$nivel_anterior},"<br>";
	for($i=1; $i<=7; $i++)
	{
		// $display_nivel1 = ($empresa->nivel1 != "1") ? 'display: none' : 'display: block';
		if($empresa->{"nivel".$i} == 1)
		{
		echo '<div id="nivel'.$i.'">'; 
			echo '<div class="form-group">
					<label class="col-md-3">';
			 echo $empresa->{"nomniv".$i}; 
			 echo '</label>';
				echo '<div class="col-md-8">';

					$sql = "SELECT codorg, CONCAT_WS(' ', codorg, descrip, markar) as descrip 
								FROM   nomnivel".$i."";
						if($i>1)
						{
							$nivel_anterior = "codnivel".($i-1);
							$gerencia = isset($integrante->{$nivel_anterior}) ? $integrante->{$nivel_anterior} : '' ;
							//$sql .= " WHERE gerencia='".$gerencia."' ";
						}
					//echo $sql,"<br>";
					$res = $db->query($sql);
				echo '	<select name="codnivel'.$i.'" id="codnivel'.$i.'" class="form-control form-control-inline input-medium">';
						if($operacion=='agregar' || $res->num_rows==0 || (isset($integrante->{"codnivel".$i}) && $integrante->{"codnivel".$i}==0))
							echo "<option value=''>Seleccione ".$empresa->{"nomniv".$i}."</option>";

						while($fila = $res->fetch_assoc())
						{
							if(isset($integrante) && $integrante->{"codnivel".$i}==$fila['codorg'])
								echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
							else
								echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
						}
				echo '	</select>
					</div><br>
				</div>
			</div>
			';
			echo "<br>";

		}
	}

echo '<div class="form-group">
<label class="control-label col-md-3">Categoría<span class="required">*</span></label>
			<div class="col-md-9">';
					$sql = "SELECT codorg, descrip FROM nomcategorias";
					$res = $db->query($sql);
echo '<select name="codcat" id="codcat" class="form-control form-control-inline input-medium">';
						//if($operacion=='agregar')
						echo "<option value=''>Seleccione una categor&iacute;a</option>";

						while($fila = $res->fetch_assoc())
						{
							if(isset($integrante) && $integrante->codcat==$fila['codorg'])
								echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
							else
								echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
						}
echo '				</select>
			</div>
		</div>';





 ?>