<?php

include '../modelos/MySQL.php';
$accion=$_GET['Accion'];
$codorg = isset($_GET['codorg']) ? $_GET['codorg'] : '';
$descrip = isset($_GET['descrip']) ? $_GET['descrip'] : '';
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
if ($_GET['pos']!='')
{ 
	
	  $ini=$_GET['pos'];
	  echo '
	  <html>
	  	<meta charset="UTF-8">

	<script type="text/javascript" src="../../../includes/js/jquery-1.11.0.min.js"></script>	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="../../../includes/js/bootstrap/css/bootstrap.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="../../../includes/js/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../../../includes/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="../../../includes/js/profesion2.js"></script>

<div class="container-fluid">
	<div class="row">
		<div  class="col-md-4">
			  <div class="form-group">
			    <label class="sr-only" for="buscar">Buscar</label>
			    <div class="input-group">
			      <input type="text" class="form-control" id="buscar" placeholder="Buscar frase">
			      <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>

			    </div>

			  </div>

		</div>
		<div class="col-md-2">   <button   id = "Buscar" type="submit" class="btn btn-primary">Buscar</button>
		</div>
		<div class="col-md-6">
			<div class="text-right">
				<!-- Button trigger modal -->	
				<button id="AgregarModal" type="button" class="btn btn-primary btn-primary" data-toggle="modal" data-target="#myModal">
				Agregar
				</button>
			</div>
		</div>
		
	</div>
	


	<div class="row">
		<div  class="col-md-12">
			<div id="mensajes">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
		
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Profesion</h4>
						</div>
						<div class="modal-body">
							
							<input id="AgregarDescripcion" class="form-control" rows="3" placeholder="Agregar Profesión"></input>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button id="Agregar" type="button" class="btn btn-primary">Guardar</button>
						</div>
					</div>
				</div>
			</div>
			<div id="profesion">
			</div>
		</div>
	</div>
</div>
';
	$url=basename($_SERVER ["PHP_SELF"]);
}
	else
	  {$ini=1;	$url = "../../includes/clases/controladores/".basename($_SERVER ["PHP_SELF"]);}

switch ($accion) 
{
	case 'Listar':
		Listar($ini,$url);
		break;
	case 'Agregar':
		Agregar($descrip);
		break;
	case 'Editar':
		Editar($codorg,$descrip);
		break;
	case 'Buscar':
		Buscar($buscar,$ini,$url);
		break;	
	case 'Eliminar':
		Eliminar($codorg);
		break;
}

function Listar($ini,$url)
{
	$order="codorg ASC";
	

	$limit_end = 10;
	$init = ($ini-1) * $limit_end;
	$consulta = "SELECT * 
		FROM nomprofesiones
		ORDER BY ".$order."  
		LIMIT ".$init." , ".$limit_end;	
	$objConexion = new MySQL();
	$objConexion->ejecutarQuery($consulta);
	$datos2 = $objConexion->getMatrizCompleta();


		$consulta = "SELECT *
					FROM nomprofesiones";	
	$objConexion->ejecutarQuery($consulta);
	$datos = $objConexion->getMatrizCompleta();


	  $total = ceil(count($datos)/$limit_end);
  echo "<div id='busqueda'>";

  echo "<table border='1' class='table table-bordered table-hover'>";
  echo "<thead>";
  echo "<tr>";
  echo "<th><b>Código</b></th>";
  echo "<th><b>Descripción</b></th>";
  echo "<th><b></b></th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody>";
	for ($i=0; $i < count($datos2); $i++) 
	{ 
		# code...

		echo "<tr class='registro'  idelim='".$datos2[$i]['codorg']."'>
				<td class='codigo' data-idcod='".$datos2[$i]['codorg']."'>".$datos2[$i]['codorg']."</td>
				<td>".$datos2[$i]['descrip']."</td>
				<td  id='linea' class='text-center'>

					<a id='editar' href='profesion-edit.php?codorg=".$datos2[$i]['codorg']."&descrip=".$datos2[$i]['descrip']."'>
						<div class='glyphicon glyphicon-edit' aria-hidden='true'></div>
					</a>

					<div id='eliminar' class='glyphicon glyphicon-trash' aria-hidden='true'></div>

				</td>
			</tr>
			";		
	}
	  echo "</tbody>";
  echo "<table>";
  
 /* numeración de registros [importante]*/
  echo "<nav><div class='text-center'>";
  echo "<ul class='pagination'>";
  /****************************************/
  if(($ini - 1) == 0)
  {
    echo "<li><a href='#'>&laquo;</a></li>";
  }
  else
  {
    echo "<li><a href='$url?pos=".($ini-1)."&Accion=Listar'><b>&laquo;</b></a></li>";
  }
  /****************************************/
  for($k=1; $k <= $total; $k++)
  {
    if($ini == $k)
    {
      echo "<li><a href='#'><b>".$k."</b></a></li>";
    }
    else
    {										

      echo "<li><a href='$url?pos=$k&Accion=Listar'>".$k."</a></li>";
    }
  }
  /****************************************/
  if($ini == $total)
  {
    echo "<li><a href='#'>&raquo;</a></li>";
  }
  else
  {
    echo "<li><a href='$url?pos=".($ini+1)."&Accion=Listar'><b>&raquo;</b></a></li>";
  }
  /*******************END*******************/
  echo "</ul>";
  echo "</div></nav>
    </div></html>
";

}
function Buscar($cadena,$ini,$url)
{
	$order="codorg ASC";
	$limit_end = 10;
	$init = ($ini-1) * $limit_end;
	$consulta="SELECT * 
	FROM nomprofesiones
	WHERE descrip LIKE  '%".$cadena."%' 		
	ORDER BY ".$order."  
		LIMIT ".$init." , ".$limit_end;
	$objConexion = new MySQL();
	$objConexion->ejecutarQuery($consulta);
	$datos2 = $objConexion->getMatrizCompleta();

	$consulta="	SELECT *
				FROM nomprofesiones
					WHERE descrip LIKE  '%".$cadena."%' AND codorg='".$cadena."'";
	$objConexion->ejecutarQuery($consulta);
	$datos = $objConexion->getMatrizCompleta();


	  $total = ceil(count($datos)/$limit_end);
  echo "<div id='busqueda'>";

  echo "<table id='mitabla' border='1' class='table table-bordered table-hover'>";
  echo "<thead>";
  echo "<tr>";
  echo "<th><b>Código</b></th>";
  echo "<th><b>Descripción</b></th>";
  echo "<th><b></b></th>";
  echo "</tr>";
  echo "</thead>";
  echo "<tbody>";
	for ($i=0; $i < count($datos2); $i++) 
	{ 
		# code...

		echo "<tr class='registro'  idelim='".$datos2[$i]['codorg']."'>
				<td class='codigo' data-idcod='".$datos2[$i]['codorg']."'>".$datos2[$i]['codorg']."</td>
				<td>".$datos2[$i]['descrip']."</td>
				<td  id='linea' class='text-center'>

					<a id='editar' href='profesion-edit.php?codorg=".$datos2[$i]['codorg']."&descrip=".$datos2[$i]['descrip']."'>
						<div class='glyphicon glyphicon-edit' aria-hidden='true'></div>
					</a>

					<div id='eliminar' class='glyphicon glyphicon-trash' aria-hidden='true'></div>

				</td>
			</tr>
			";		
	}
	  echo "</tbody>";
  echo "<table>";
  
 /* numeración de registros [importante]*/
  echo "<nav><div class='text-center'>";
  echo "<ul class='pagination'>";
  /****************************************/
  if(($ini - 1) == 0)
  {
    echo "<li><a href='#'>&laquo;</a></li>";
  }
  else
  {

    echo "<li><a href='$url?pos=".($ini-1)."&Accion=Buscar&buscar=".$cadena."'><b>&laquo;</b></a></li>";
  }
  /****************************************/
  for($k=1; $k <= $total; $k++)
  {
    if($ini == $k)
    {
      echo "<li><a href='#'><b>".$k."</b></a></li>";
    }
    else
    {

      echo "<li><a href='$url?pos=$k&Accion=Buscar&buscar=".$cadena."'>".$k."</a></li>";
    }
  }
  /****************************************/
  if($ini == $total)
  {
    echo "<li><a href='#'>&raquo;</a></li>";
  }
  else
  {
    echo "<li><a href='$url?pos=".($ini+1)."&Accion=Buscar&buscar=".$cadena."'><b>&raquo;</b></a></li>";
  }
  /*******************END*******************/
  echo "</ul>";
  echo "</div></nav>
  </div></html>
";
}
function Agregar($descrip)
{
	$objConexion = new MySQL();
	$consulta = "SELECT MAX( codorg ) as ultimo
	FROM nomprofesiones";
	$objConexion->ejecutarQuery($consulta);
	$datos = $objConexion->getMatrizCompleta();
	$ultimo = $datos[0]['ultimo'] + 1;
	$resp2= $objConexion->ingresar_profesion($ultimo,$descrip); 

	if($resp2)
	{
		echo '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label	="Close"><span aria-hidden="true">&times;</span></button> Datos insertados correctamente</div>';
	}
	else
	{
		echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>No se insertaron datos</div>';
	}
	
	
}
function Editar($codorg,$descrip)
{

			 $objConexion = new MySQL();
	$datos = 	$objConexion->editar_profesion($codorg,$descrip);

	if($datos)
	{
		echo '<div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Datos editados correctamente</div>';
	}
	else
	{
		echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>No se editaron datos</div>';
	}
}
function Eliminar()
{
	$consulta = "delete from nomprofesiones where codorg='".$codorg."'";
	$objConexion = new MySQL();
	$datos = $objConexion->ejecutarQuery($consulta);

	if($datos)
	{
		echo '<div class="alert alert-danger alert-success" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button> Datos insertados correctamente</div>';
	}
	else
	{
		echo '<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>No se insertaron datos</div>';
	}
}	


?>