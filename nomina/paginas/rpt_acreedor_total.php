<?php
session_start();
ob_start();
//$termino=$_SESSION['termino'];
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");
ob_clean();
$conexion= new bd($_SESSION['bd']);

$finicio = $_GET['fechainicio'] ; 
$finicio = date('Y-m-d',strtotime($finicio));
$ffin = $_GET['fechafinal'] ; 
$ffin = date('Y-m-d',strtotime($ffin));
/*
$date =explode("/", $finicio);
$finicio = $date[2].'-'.$date[1].'-'.$date[0] ;
 
$date1 = explode("/", $ffin);
$ffin = $date1[2].'-'.$date1[1].'-'.$date1[0] ;*/
 /*
$encabezado = "SELECT  codtip,descrip FROM `nomtipos_nomina`" ;

$resultado_encabezado=$conexion->query($encabezado);

while($encabezado_final = $resultado_encabezado->fetch_array())
{*/
    $encabezado_f = $encabezado_final['descrip'] ;
    $hoy = date("Y-m-d"); 
    $consulta= "SELECT   C.descrip,  A.ficha ,  B.nombres , B.apellidos , D.fechapago ,  A.monto,C.formula,D.periodo_ini,D.periodo_fin  , C.ccosto
    FROM nom_movimientos_nomina A  JOIN nompersonal B ON (A.ficha= B.ficha)  
    JOIN nomconceptos C ON (A.codcon = C.codcon)  JOIN nom_nominas_pago D 
    ON (D.codnom = A.codnom AND D.codtip = A.tipnom)  
    where A.codcon >= 500 AND A.codcon <= 600 AND  D.periodo_ini >= '".$finicio."' AND D.periodo_fin <= '".$ffin."' AND A.tipnom in (1,2,3,4)
    order by C.descrip , A.ficha" ;

// <tr><td colspan ="4" align="center"><strong>Ficha</strong> : '.$fetch[0].' <strong>Nombre : </strong>'.$fetch[1].'</td></tr>
    $resultado_tiping=$conexion->query($consulta);
    $aux = 0 ; 
    $aux1 = 0 ; 
    $total = 0 ;
    $varacreeadoractual = '' ;
    $varacreeadoranterior = '' ;

    $vectoracreedor = array() ;
    $vectortotal = array() ; 
    $contd = 0 ;
    while($fetch=$resultado_tiping->fetch_array()) {
        if($aux == 0)
        {
            $aux = 1 ;
        
    
        $tbHtml .= '<table border=1>
                <header>
            
                <tr><td colspan ="6" ></td></tr>
                <tr><td colspan ="6" align="center" >'.utf8_decode($encabezado_f).'</td></tr>
                <tr><td colspan ="6" align="center" >Reportes de Acreedores</td></tr>
                <tr><td colspan ="6" align="center"  >Fecha '.$hoy.'</td></tr>
                <tr><td colspan ="6" align="center"  >Desde '.$finicio.' Hasta '.$ffin.'</td></tr>
                <tr>
                    <th>Ficha</th>
                    <th style="width:200px">Nombre</th>
                    <th style="width:200px">Apellidos</th>
                    <th style="width:100px">Fecha</th>
                    <th style="width:100px">Monto</th>
                    <th style="width:100px">Saldo</th>
                    </tr>
                </header>';


        }	

    $varacreeadoractual = $fetch[0] ;
    if($varacreeadoractual != $varacreeadoranterior)
    { 

        if($aux1 != 0)
        {
    
        $tbHtml .=  '<tr><td colspan ="4" align="center" ><strong>Total</strong></td><td align="right"><strong>'.$totals.'</strong></td></tr>' ;



        }
        $aux1 = 1 ;
        $totals = 0 ; 

    $tbHtml .=  '<tr><td colspan ="6" align="center" ><strong>'.$fetch[0].'</strong></td></tr>' ;
    
    $varacreeadoranterior = $fetch[0] ;

    }
    $formula = explode('(', $fetch[6]);
    $formulas = explode(',', $formula[1]);
    $acreedor = $fetch['ccosto'];
    $consulta3="SELECT salfinal,npd.fechaven 
    FROM nomprestamos_detalles npd INNER JOIN nomprestamos_cabecera npc on (npd.numpre=npc.numpre) 
    where npc.codigopr='{$acreedor}'  AND npc.ficha='{$fetch[1]}' AND npd.fechaven >= '{$fetch[periodo_ini]}' AND npd.fechaven <= '{$fetch[periodo_fin]}' AND npc.estadopre in ('Pendiente','Cancelada')";
    $res_sal=$conexion->query($consulta3);
    $salfinal=$res_sal->fetch_array();
    if ($salfinal[fechaven]!="") {
    $fecha_ven = date('d-m-Y',strtotime($salfinal[fechaven]));
    }
    else{
    $fecha_ven != "";
    }
    if ($acreedor!="") 
    { 
        $tbHtml .= '<tr><td>'.$fetch[1].'</td><td>'.utf8_decode($fetch['nombres']).'</td><td>'.utf8_decode($fetch['apellidos']).'</td><td>'.$fecha_ven.'</td><td align="right">'.number_format($fetch[5],2).'</td><td>'.$salfinal[salfinal].'</td></tr>';

    }
    else
    {
    $tbHtml .= '<tr><td>'.$fetch[1].'</td><td>'.utf8_decode($fetch['nombres']).'</td><td>'.utf8_decode($fetch['apellidos']).'</td><td>'.$fecha_ven.'</td><td align="right">'.number_format($fetch[5],2).'</td><td></td></tr>';

    }
    $total  += $fetch[5] ; 
    $totals += $fetch[5] ;

    } 
//}
$tbHtml .=  '<tr><td colspan ="4" align="center" ><strong>Total</strong></td><td align="right"><strong>'.$totals.'</strong></td></tr>' ;

$tbHtml .= '<tr><td></td><td></td><td></td><td><strong>Total</strong></td><td align="right">$'.number_format ($total,2)."</td><td></td></tr>";

$tbHtml .= "</html>";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=acreedores.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $tbHtml;