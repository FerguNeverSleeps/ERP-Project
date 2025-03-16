<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();
/*
echo $_SESSION['_personal']," ",$_SESSION['_personal2'],"<BR>";
echo $_SESSION['_relacion112']," ",$_SESSION['_relacion122'],"<BR>";
echo $_SESSION['_relacion212']," ",$_SESSION['_relacion222']," ",$_SESSION['_relacion232']," ",$_SESSION['_relacion242']," ",$_SESSION['_relacion252'],"<BR>";
echo $_SESSION['_relacion312']," ",$_SESSION['_relacion322'],"<BR>";
echo $_SESSION['_relacion412']," ",$_SESSION['_relacion422']," ",$_SESSION['_relacion432'],"<BR>";
echo $_SESSION['_relacion512']," ",$_SESSION['_relacion522']," ",$_SESSION['_relacion532']," ",$_SESSION['_relacion542'],"<BR>";
echo $_SESSION['_relacion612']," ",$_SESSION['_relacion622']," ",$_SESSION['_relacion632']," ",$_SESSION['_relacion642'],"<BR>";
echo $_SESSION['_relacion712']," ",$_SESSION['_relacion722']," ",$_SESSION['_relacion732']," ",$_SESSION['_relacion742'],"<BR>";
echo $_REQUEST['relacion113']," ",$_REQUEST['opinion1']," ",$_REQUEST['opinion2']," ",$_REQUEST['opinion3']," ",$_REQUEST['comentario1'],"<BR>";*/
$evaluador=$_SESSION['_personal'];
$evaluado=$_SESSION['_personal2'];
$pregunta112=$_SESSION['_relacion112'];
$pregunta122=$_SESSION['_relacion122'];
$pregunta212=$_SESSION['_relacion212'];
$pregunta222=$_SESSION['_relacion222'];
$pregunta232=$_SESSION['_relacion232'];
$pregunta242=$_SESSION['_relacion242'];
$pregunta252=$_SESSION['_relacion252'];
$pregunta312=$_SESSION['_relacion312'];
$pregunta322=$_SESSION['_relacion322'];
$pregunta412=$_SESSION['_relacion412'];
$pregunta422=$_SESSION['_relacion422'];
$pregunta432=$_SESSION['_relacion432'];
$pregunta512=$_SESSION['_relacion512'];
$pregunta522=$_SESSION['_relacion522'];
$pregunta532=$_SESSION['_relacion532'];
$pregunta542=$_SESSION['_relacion542'];
$pregunta612=$_SESSION['_relacion612'];
$pregunta622=$_SESSION['_relacion622'];
$pregunta632=$_SESSION['_relacion632'];
$pregunta642=$_SESSION['_relacion642'];
$pregunta712=$_SESSION['_relacion712'];
$pregunta722=$_SESSION['_relacion722'];
$pregunta732=$_SESSION['_relacion732'];
$pregunta742=$_SESSION['_relacion742'];
$pregunta113=$_REQUEST['relacion113'];
$opinion1=$_REQUEST['opinion1'];
$opinion2=$_REQUEST['opinion2'];
$opinion3=$_REQUEST['opinion3'];
$comentario1=$_REQUEST['comentario1'];
$fecha=date("Y-m-d");
$SQL="INSERT INTO nom_eval_desemp(evaluador,fecha_desemp, evaluado, pregunta112, pregunta122, pregunta212, pregunta222, pregunta232, pregunta242, pregunta252, 
	pregunta312, pregunta322, pregunta412, pregunta422, pregunta432, pregunta512, pregunta522, pregunta532, 
	pregunta542, pregunta612, pregunta622, pregunta632, pregunta642, pregunta712, pregunta722, pregunta732, 
	pregunta742, pregunta113, opinion1, opinion2, opinion3, comentario1,estado) 
VALUES (".$evaluador.",'".$fecha."',".$evaluado.",".$pregunta112.",".$pregunta122.",".$pregunta212.",".$pregunta222.",".$pregunta232.",".$pregunta242.",".$pregunta252.",
	".$pregunta312.",".$pregunta322.",".$pregunta412.",".$pregunta422.",".$pregunta432.",".$pregunta512.",".$pregunta522.",".$pregunta532.",
	".$pregunta542.",".$pregunta612.",".$pregunta622.",".$pregunta632.",".$pregunta642.",".$pregunta712.",".$pregunta722.",".$pregunta732.",
	".$pregunta742.",".$pregunta113.",'".$opinion1."','".$opinion2."','".$opinion3."','".$comentario1."','Por evaluar')";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		unset($_SESSION['_relacion112']);
		unset($_SESSION['_relacion122']);
		unset($_SESSION['_relacion212']);
		unset($_SESSION['_relacion222']);
		unset($_SESSION['_relacion232']);
		unset($_SESSION['_relacion242']);
		unset($_SESSION['_relacion252']);
		unset($_SESSION['_relacion312']);
		unset($_SESSION['_relacion322']);
		unset($_SESSION['_relacion412']);
		unset($_SESSION['_relacion422']);
		unset($_SESSION['_relacion432']);
		unset($_SESSION['_relacion512']);
		unset($_SESSION['_relacion522']);
		unset($_SESSION['_relacion532']);
		unset($_SESSION['_relacion542']);
		unset($_SESSION['_relacion612']);
		unset($_SESSION['_relacion622']);
		unset($_SESSION['_relacion632']);
		unset($_SESSION['_relacion642']);
		unset($_SESSION['_relacion712']);
		unset($_SESSION['_relacion722']);
		unset($_SESSION['_relacion732']);
		unset($_SESSION['_relacion742']);

		echo "<script>alert('Evaluación agregada exitosamente');
		location.href='evaluacion_empleado_list.php'</script>";


	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='evaluacion_empleado.php'</script>";			
	}
?>