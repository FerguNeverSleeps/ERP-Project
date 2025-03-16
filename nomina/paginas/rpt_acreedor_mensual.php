<?php
session_start();
ob_start();
//$termino=$_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
$conexion=conexion();


$anio           = (empty($_GET['anio'])) ? 0:   $_GET['anio'];
$mes           = (empty($_GET['mes'])) ? 0:   $_GET['mes'];
$tipos           = (empty($_GET['tipos'])) ? 0:   $_GET['tipos'];
/*$date = explode("/", $finicio);
$finicio = $date[2].'-'.$date[1].'-'.$date[0] ;
 
$date1 = explode("/", $ffin);
$ffin = $date1[2].'-'.$date1[1].'-'.$date1[0] ;*/
 
function meses( $mes ) 
{
    $meses  = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $mes_letras=$meses[$mes-1];
    return $mes_letras; 
} 
$tipos = explode(',', $tipos);
$consulta_AND = " AND (";
if (is_array($tipos) || is_object($tipos))
{
    foreach ($tipos as $planilla) {
        $temporal = explode('_', $planilla);
        //$planillas[] = array('codigo' => $temporal[0],'tipo' => $temporal[1] );
        $consulta_AND .= "(A.codnom='".$temporal[0]."' AND A.tipnom = '".$temporal[1]."') OR ";
    }
}
else
{
    echo "<script>alert('Error al generar el reporte');";
    echo "location.href='consulta_acreedor_mensual.php';</script>";

}

$consulta_AND .= '****';
$consulta_AND = str_replace(' OR ****', '', $consulta_AND);
$consulta_AND .= ")";
 



$encabezado = "SELECT  descrip FROM `nomtipos_nomina`  where codtip ='{$codtip}'" ;

$resultado_encabezado=query($encabezado,$conexion);
$encabezado_final = fetch_array($resultado_encabezado) ;
$encabezado_f = $encabezado_final[0] ;
$hoy = date("Y-m-d"); 

 

 $consulta= "SELECT   
  C.descrip,  
  A.ficha ,  
  B.nombres , 
  B.apellidos , 
  date_format(D.fechapago,'%m-%Y') fechapago,
  SUM(A.monto) as monto,
  C.formula,
  D.periodo_fin  
FROM 
  nom_movimientos_nomina A  
  join nompersonal B on (A.ficha= B.ficha)  
  join nomconceptos C on (A.codcon = C.codcon)  
  join nom_nominas_pago D on (D.codnom = A.codnom AND D.tipnom=A.tipnom)   
WHERE A.codcon >= 500 
  and A.codcon < 600 
  and D.anio_sipe >= '".$anio."' 
   and D.mes_sipe <= '".$mes."' ";

$consulta .= $consulta_AND;
$consulta .=" 
GROUP by A.ficha,A.codcon
ORDER by C.descrip , A.ficha" ;
  //             <tr><td colspan ="4" align="center"><strong>Ficha</strong> : '.$fetch[0].' <strong>Nombre : </strong>'.$fetch[1].'</td></tr>
$resultado_tiping=query($consulta,$conexion);
$aux = 0 ; 
$aux1 = 0 ; 
$total = 0 ;
$varacreeadoractual = '' ;
$varacreeadoranterior = '' ;

$vectoracreedor = array() ;
$vectortotal = array() ; 
$contd = 0 ;
while($fetch=fetch_array($resultado_tiping)) {
	if($aux == 0)
	{
		$aux = 1 ;
    
   
$tbHtml  = '<table border=1>';
$tbHtml .='<header>';

$tbHtml .='<tr><td colspan ="7" ></td></tr>';
$tbHtml .='<tr><td colspan ="7" align="center" ><b>'.$encabezado_f.'</b></td></tr>';
$tbHtml .='<tr><td colspan ="7" align="center" ><b>Reportes de Acreedores</b></td></tr>';
$tbHtml .=' <tr><td colspan ="7" align="center"  ><b>Fecha '.$hoy.'</b></td></tr>';
//$tbHtml .=' <tr><td colspan ="7" align="center"  >Desde '.$finicio.' Hasta '.$ffin.'</td></tr>';
$tbHtml .='<tr>';
$tbHtml .=' <th>Ficha</th>';
$tbHtml .='  <th style="width:200px">Nombre</th>';
$tbHtml .=' <th style="width:200px">Apellidos</th>';
$tbHtml .=' <th style="width:100px">Fecha</th>';
$tbHtml .=' <th style="width:150px">Cuenta</th>';
$tbHtml .=' <th style="width:100px">Monto</th>';
$tbHtml .=' <th style="width:100px">Saldo</th>';
$tbHtml .=' </tr>';
$tbHtml .='</header>';


	}	

$varacreeadoractual = $fetch[0] ;
if($varacreeadoractual != $varacreeadoranterior)
{ 

    if($aux1 != 0)
    {
   
    $tbHtml .=  '<tr><td colspan ="5" align="center" ><strong>Total</strong></td><td align="right">'.$totals.'</td></tr>' ;



    }
    $aux1 = 1 ;
    $totals = 0 ; 

 $tbHtml .=  '<tr><td colspan ="7" align="center" ><strong>'.$fetch[0].'</strong></td></tr>' ;
 
 $varacreeadoranterior = $fetch[0] ;

}
$formula = explode('(', $fetch[6]);
$formulas = explode(',', $formula[1]);
$acreedor = explode(')', $formulas[3]);
if (($acreedor[0]!= "") AND (count($formulas)>2)) 
{ 
     $consulta3="select npd.salfinal, CAST(npc.cuenta_destino AS char) cuenta_destino from nomprestamos_detalles npd join nomprestamos_cabecera npc on (npd.numpre=npc.numpre) where npc.codigopr='{$acreedor[0]}'  and npc.ficha='{$fetch[1]}' and npd.fechaven = '{$fetch[7]}' AND npc.estadopre in ('Pendiente','Cancelada')";
    $res_sal=query($consulta3,$conexion);
    $salfinal=fetch_array($res_sal);
    $tbHtml .= '<tr><td>'.$fetch[1].'</td><td>'.$fetch[2].'</td><td>'.$fetch[3].'</td><td>'.$fetch[4].'</td><td><b>&nbsp;'.$salfinal["cuenta_destino"].'</b></td><td align="right">'.number_format($fetch[5],2).'</td><td>'.$salfinal["salfinal"].'</td></tr>';

}
else
{
$tbHtml .= '<tr><td>'.$fetch[1].'</td><td>'.$fetch[2].'</td><td>'.$fetch[3].'</td><td><td><td><td><td>'.$fetch[4].'</td><td align="right">'.number_format($fetch[5],2).'</td><td></td></tr>';

}
$total  += $fetch[5] ; 
$totals += $fetch[5] ;

}  
$tbHtml .=  '<tr><td colspan ="6" align="center" ><strong>Total</strong></td><td align="right">'.$totals.'</td></tr>' ;

$tbHtml .= '<tr><td></td><td></td><td></td><td><td><td><strong>Total General</strong></td><td align="right">$'.number_format ($total,2)."</td></tr>";

$tbHtml .= "</html>";
ob_clean();
$nombre_archivo =  "ACREEDORES_".meses($mes)."_".$anio.".xls";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$nombre_archivo);
header("Pragma: no-cache");
header("Expires: 0");
echo $tbHtml;