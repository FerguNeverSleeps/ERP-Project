<?php
	$nombreCompleto=$_GET['nombre'];
	$ci=$_GET['ci'];

?>
<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>Funcionario: <?=$nombreCompleto?> - Cédula de Identidad: <?=$ci?></title>
	<link href="assets/css/styles.css" rel="stylesheet"/>
</head>
 <style>
.nav-tabs > li {
    float:none;
    display:inline-block;
    zoom:1;
}

.nav-tabs {
    text-align:right;
} 
.boton-bottom-simple {
    width: 30px;
    background: rgba(255,0,0,0.4);
    position:absolute;
    text-align:center;
    bottom:0;
    right:0;
    z-index: 100;
 }
.boton-top-multiple {
    width: 60px;
    background: rgba(255,0,0,0.4);
    position:absolute;
    text-align:center;
    top:0;
    right:0;
 }
.icono-bottom {
	content: '×';
    width: 30px;
    height: 25px;
    padding:2px;
    position:relative;
    bottom:0;
    right:0;
 }
 .btn {
  border: none; /* Remove borders */
  color: white; /* Add a text color */
  padding: 14px 28px; /* Add some padding */
  cursor: pointer; /* Add a pointer cursor on mouse-over */
}

.success {background-color: #04AA6D;} /* Green */
.success:hover {background-color: #46a049;}
.info {background-color: #2196F3;} /* Blue */
.info:hover {background: #0b7dda;}

 </style>
<body style="background-color: #fff;">

	<div class="filemanager">
	<input type="hidden" name="hid_ci" id="hid_ci" value="<?=$ci?>">
	<input type="hidden" name="hid_nombre" id="hid_nombre" value="<?=$nombreCompleto?>">
		<div class="boton">
        <button type="button" class="btn info" onclick="javascript: document.location.href='<?php echo '../../paginas/maestro_personal.php'; ?>'">Regresar</button>

		</div>

		<div class="breadcrumbs" style="color: #373743;" ></div>

		<ul class="data"></ul>

		<div class="nothingfound">
			<div class="nofiles"></div>
			<span>Sin Adjuntos</span>
		</div>
		

	</div>

	

	
	<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="assets/js/script.js?<?= date("sih") ?>"></script>

</body>
</html>