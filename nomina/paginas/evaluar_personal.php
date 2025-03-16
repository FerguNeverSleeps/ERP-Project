<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("func_bd.php");	

session_start();
ob_start();/*
echo $_SESSION['_personal'],'" "',$_SESSION['_personal2'],'"<BR>";
echo $_SESSION['_relacion112'],'" "',$_SESSION['_relacion122'],'"<BR>";
echo $_SESSION['_relacion212'],'" "',$_SESSION['_relacion222'],'" "',$_SESSION['_relacion232'],'" "',$_SESSION['_relacion242'],'" "',$_SESSION['_relacion252'],'"<BR>";
echo $_SESSION['_relacion312'],'" "',$_SESSION['_relacion322'],'"<BR>";
echo $_SESSION['_relacion412'],'" "',$_SESSION['_relacion422'],'" "',$_SESSION['_relacion432'],'"<BR>";
echo $_SESSION['_relacion512'],'" "',$_SESSION['_relacion522'],'" "',$_SESSION['_relacion532'],'" "',$_SESSION['_relacion542'],'"<BR>";
echo $_SESSION['_relacion612'],'" "',$_SESSION['_relacion622'],'" "',$_SESSION['_relacion632'],'" "',$_SESSION['_relacion642'],'"<BR>";
echo $_SESSION['_relacion712'],'" "',$_SESSION['_relacion722'],'" "',$_SESSION['_relacion732'],'" "',$_SESSION['_relacion742'],'"<BR>";
echo $_REQUEST['relacion113'],'" "',$_REQUEST['opinion1'],'" "',$_REQUEST['opinion2'],'" "',$_REQUEST['opinion3'],'" "',$_REQUEST['comentario1'],'"<BR>";*/
$id_personal=$_SESSION['_personal'];
$pregunta111=$_SESSION['_relacion111'];
$pregunta121=$_SESSION['_relacion121'];
$frecuencia=$_SESSION['_frecuencia'];
$pregunta112=$_SESSION['_relacion112'];
$pregunta122=$_SESSION['_relacion122'];
$pregunta132=$_SESSION['_relacion132'];
$pregunta142=$_SESSION['_relacion142'];
$pregunta212=$_SESSION['_relacion212'];
$pregunta222=$_SESSION['_relacion222'];
$pregunta232=$_SESSION['_relacion232'];
$pregunta242=$_SESSION['_relacion242'];
$pregunta252=$_SESSION['_relacion252'];
$pregunta262=$_SESSION['_relacion262'];
$pregunta312=$_SESSION['_relacion312'];
$pregunta322=$_SESSION['_relacion322'];
$comentario1=$_SESSION['_comentario1'];
$pregunta113=$_SESSION['_relacion113'];
$pregunta123=$_SESSION['_relacion123'];
$pregunta133=$_SESSION['_relacion133'];
$pregunta143=$_SESSION['_relacion143'];
$pregunta153=$_SESSION['_relacion153'];
$pregunta213=$_SESSION['_relacion213'];
$pregunta223=$_SESSION['_relacion223'];
$pregunta233=$_SESSION['_relacion233'];
$comentario2=$_SESSION['_comentario2'];
$comentario3=$_SESSION['_comentario3'];
$pregunta114=$_SESSION['_relacion114'];
$pregunta124=$_SESSION['_relacion124'];
$pregunta134=$_SESSION['_relacion134'];
$pregunta144=$_SESSION['_relacion144'];
$pregunta154=$_SESSION['_relacion154'];
$pregunta164=$_SESSION['_relacion164'];
$pregunta174=$_SESSION['_relacion174'];
$pregunta175=$_SESSION['_relacion175'];
$comentario4=$_SESSION['_comentario4'];
$pregunta115=$_REQUEST['relacion115'];
$pregunta125=$_REQUEST['relacion125'];
$pregunta135=$_REQUEST['relacion135'];
$pregunta145=$_REQUEST['relacion145'];
$pregunta155=$_REQUEST['relacion155'];
$pregunta165=$_REQUEST['relacion165'];
$pregunta175=$_REQUEST['relacion175'];
$comentario5=$_REQUEST['comentario5'];
$fecha=date("Y-m-d");
$SQL="INSERT INTO nom_eval_personal(id_personal,fecha_personal, pregunta111, pregunta121, frecuencia, pregunta112, pregunta122, pregunta132, 
	pregunta142, pregunta212, pregunta222, pregunta232, pregunta242, pregunta252, pregunta262, pregunta312, pregunta322, comentario1,
	pregunta113, pregunta123, pregunta133, pregunta143, pregunta153, pregunta213, pregunta223, pregunta233, comentario2, comentario3,
	pregunta114, pregunta124, pregunta134, pregunta144, pregunta154, pregunta164, pregunta174, comentario4, pregunta115, pregunta125,
	pregunta135, pregunta145, pregunta155, pregunta165, pregunta175, comentario5,Estado) 
	VALUES ($id_personal,'".$fecha."','".$pregunta111."','".$pregunta121."','".$frecuencia."','".$pregunta112."','".$pregunta122."','".$pregunta132."',
		'".$pregunta142."','".$pregunta212."','".$pregunta222."','".$pregunta232."','".$pregunta242."','".$pregunta252."','".$pregunta262."',
		'".$pregunta312."','".$pregunta322."','".$comentario1."','".$pregunta113."','".$pregunta123."','".$pregunta133."','".$pregunta143."',
		'".$pregunta153."','".$pregunta213."','".$pregunta223."','".$pregunta233."','".$comentario2."','".$comentario3."','".$pregunta114."',
		'".$pregunta124."','".$pregunta134."','".$pregunta144."','".$pregunta154."','".$pregunta164."','".$pregunta174."','".$comentario4."',
		'".$pregunta115."','".$pregunta125."','".$pregunta135."','".$pregunta145."','".$pregunta155."','".$pregunta165."','".$pregunta175."',
		'".$comentario5."','Por Atender')";

		$result=sql_ejecutar($SQL);	
		if($result)
		{
			unset($_SESSION['_personal']);
			unset($_SESSION['_relacion111']);	
			unset($_SESSION['_relacion121']);
			unset($_SESSION['_frecuencia']);
			unset($_SESSION['_relacion112']);
			unset($_SESSION['_relacion122']);
			unset($_SESSION['_relacion132']);
			unset($_SESSION['_relacion142']);
			unset($_SESSION['_relacion212']);
			unset($_SESSION['_relacion222']);
			unset($_SESSION['_relacion232']);
			unset($_SESSION['_relacion242']);
			unset($_SESSION['_relacion252']);
			unset($_SESSION['_relacion262']);
			unset($_SESSION['_relacion312']);
			unset($_SESSION['_relacion322']);
			unset($_SESSION['_comentario1']);
			unset($_SESSION['_relacion113']);
			unset($_SESSION['_relacion123']);
			unset($_SESSION['_relacion133']);
			unset($_SESSION['_relacion143']);
			unset($_SESSION['_relacion153']);
			unset($_SESSION['_relacion213']);
			unset($_SESSION['_relacion223']);
			unset($_SESSION['_comentario2']);
			unset($_SESSION['_comentario3']);
			unset($_SESSION['_relacion114']);
			unset($_SESSION['_relacion124']);
			unset($_SESSION['_relacion134']);
			unset($_SESSION['_relacion144']);
			unset($_SESSION['_relacion154']);
			unset($_SESSION['_relacion164']);
			unset($_SESSION['_relacion174']);
			unset($_SESSION['_comentario4']);
	

			echo "<script>alert('Evaluación agregada exitosamente')
			location.href='evaluacion_personal_list.php'</script>";		
		}
		else
		{
			echo $SQL;
		}



?>