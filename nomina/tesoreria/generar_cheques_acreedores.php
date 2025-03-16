<?php 
session_start();
ob_start();
$bd=$_SESSION['bd'];



//cerrar la nominaasdf

require_once '../lib/common.php';

$selectra=new bd($bd);
$nomina=$_GET[nomina];
$chequera=$_GET[chequera];

$consulta="select nmn.*, np.personal_id from nom_movimientos_nomina nmn join nompersonal np on (nmn.cedula=np.cedula) where codnom='".$nomina."' and codcon between 500 and 999";
$resultado_mov=$selectra->query($consulta);
if($resultado_mov->num_rows!=0)
{
	while($fila_mov=$resultado_mov->fetch_assoc())
	{
		$consulta="select min(cheque_id) as minimo from nomcheques where chequera='$chequera' and status='A' ";
		$resultado_ch=$selectra->query($consulta);
		$fila_ch=$resultado_ch->fetch_assoc();
		$cheque=$fila_ch[minimo];

		$consulta_upd="UPDATE nomcheques SET status='Ac', beneficiario='".$fila_mov[descrip]."', monto='".$fila_mov[monto]."', cedula_rif='".$fila_mov[cedula]."', fecha='".date("Y-m-d")."', concepto='"."Pago al Acreedor  ".$fila_mov[descrip]."', log_usr='".$_SESSION[nombre]."', tipo='2', codnom='".$nomina."', personal_id='".$fila_mov[personal_id]."', codcon='".$fila_mov[codcon]."'  WHERE cheque_id='$cheque'";
		$resultado_upd=$selectra->query($consulta_upd);
	}	
}

$consulta="update nom_nominas_pago set status_acreedores='1' where codnom='".$nomina."' ";
$resultado=$selectra->query($consulta);


?>
