<?php 
session_start();
ob_start();
$bd=$_SESSION['bd'];



//cerrar la nominaasdf

require_once '../lib/common.php';

$selectra=new bd($bd);
$nomina=$_GET[nomina];
$chequera=$_GET[chequera];


$consulta="select nnn.*, np.personal_id, np.apenom, nnp.descrip, np.forcob from nom_nomina_netos nnn join nompersonal np on (nnn.cedula=np.cedula) join nom_nominas_pago nnp on (nnp.codnom=nnn.codnom) where nnn.codnom='".$nomina."' and np.forcob='Cheque'";
$resultado_mov=$selectra->query($consulta);
if($resultado_mov->num_rows!=0)
{
	while($fila_mov=$resultado_mov->fetch_assoc())
	{
		$consulta="select min(cheque_id) as minimo from nomcheques where chequera='$chequera' and status='A' ";
		$resultado_ch=$selectra->query($consulta);
		$fila_ch=$resultado_ch->fetch_assoc();
		$cheque=$fila_ch[minimo];

		$consulta_upd="UPDATE nomcheques SET status='Ac', beneficiario='".$fila_mov[apenom]."', monto='".$fila_mov[neto]."', cedula_rif='".$fila_mov[cedula]."', fecha='".date("Y-m-d")."', concepto='"."Pago de ".$fila_mov[descrip]."', log_usr='".$_SESSION[nombre]."', tipo='1', codnom='".$nomina."', personal_id='".$fila_mov[personal_id]."', codcon='0'  WHERE cheque_id='$cheque'";
		$resultado_upd=$selectra->query($consulta_upd);
	}	
}

$consulta="update nom_nominas_pago set status_cheques='1' where codnom='".$nomina."' ";
$resultado=$selectra->query($consulta);
?>
