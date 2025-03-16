 <?php
ini_set('display_errors', FALSE);
 session_start();
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
require_once('../tcpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $fechainicio, $fechafinal, $fecharegistro, $totalAcreedorFuncionario, $codnom, $tipnom, $id,$mes,$total_sueldo_bruto,$total_descuentos,$nv1,$Descrip,$Planilla,$nv1_new,$nv1_temp,$neto,$nomemp;
	//Page header
   public function meses($mes)
    {
        $meses  = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        return $meses[$this->mes-1];
    }
    function setMes($mes)
    {
        $this->mes = $mes;
    }    
    public function setTotalAcreedores($acreedores_group)
    {
		$Conn = new bd($_SESSION[bd]);
		$acreedores = explode(',', $acreedores_group);
		$this->totalAcreedorFuncionario = 0;
		for ($i=0; $i < count($acreedores); $i++) { 
		
			$query          = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND codcon='{$acreedores[$i]}'";
			$result_salario =  $Conn->query($query);
			$fetch2         = $result_salario->fetch_array();
			$monto          = $fetch2['monto'];
			$this->totalAcreedorFuncionario += $monto;

		}

    }
    public function setPartidaNueva($parametro)
    {
    	$this->nv1_new = $parametro;
    }
    public function setPartidaTemp($parametro)
    {
    	$this->nv1_temp = $parametro;
    }
    public function compararPartida()
    {
    	if($this->nv1_new == $this->nv1_temp)
    	{
    		$this->setPartidaNueva ($this->nv1_new);
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    public function setNombrePartida($nv1)
    {
		if ($nv1== "") {
			$this->nv1 = "";
		}
		else
		{
			$Conn           = new bd($_SESSION[bd]);
			$sql            = "SELECT codorg, descrip from nomnivel1 where codorg = '{$nv1}'";
			$result_nv1 =  $Conn->query($sql);
			$fetch2         = $result_nv1->fetch_array();
			$this->nv1      = $fetch2['codorg'];
			$this->Descrip = utf8_encode($fetch2['descrip']);
		}
    }
    public function getNombrePartida()
    {		
        return $this->nv1;
    }
    public function getDescripPartida()
    {		
        return $this->Descrip;
    }
    public function getTotalAcreedores()
    {		
        return $this->totalAcreedorFuncionario;
    }
    public function getTotalNetoFuncionario($sueldo)
    {
    	return ($sueldo - $this->total_descuentos);
    }
    public function getNeto()
    {
    	return $this->neto;
    }
    public function NetoTotal()
    {
    		
        $neto_partida = $total_asign = $total_deduc = 0;
		$Conn = new bd($_SESSION[bd]);
        $query_asig         = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND tipcon = 'A'";
        $result_asig        =  $Conn->query($query_asig);
        while($fetch_asig   = $result_asig->fetch_array())
        {
            $asig_partida = $fetch_asig['monto'];
            $total_asign += $asig_partida;
        }
        $query_deduc        = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND tipcon = 'D'";
        $result_deduc       =  $Conn->query($query_deduc);
        while($fetch_deduc  = $result_deduc->fetch_array())
        {
            $deduc_partida = $fetch_deduc['monto'];
            $total_deduc  += $deduc_partida;
        }
        $neto_partida = $total_asign - $total_deduc;
        $this->neto = number_format($neto_partida, 2, '.',',');
    }
    public function getUser(){
    	return $_SESSION['usuario'];
    }
    public function getTotalPartida()
    {
    	if ($this->nv1!="") {
    		
	        $total_partida = $total_asign = $total_deduc = 0;
			$Conn = new bd($_SESSION[bd]);
	         $query_asig         = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND codnivel1='".$this->nv1."' AND tipcon = 'A'";
	        $result_asig        =  $Conn->query($query_asig);
	        while($fetch_asig   = $result_asig->fetch_array())
	        {
	            $asig_partida = $fetch_asig['monto'];
	            $total_asign += $asig_partida;
	        }
	        $query_deduc        = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND codnivel1='".$this->nv1."' AND tipcon = 'D' ";
	        $result_deduc       =  $Conn->query($query_deduc);
	        while($fetch_deduc  = $result_deduc->fetch_array())
	        {
	            $deduc_partida = $fetch_deduc['monto'];
	            $total_deduc  += $deduc_partida;
	        }
	         $total_partida = $total_asign - $total_deduc;

	        return $total_partida;
    	}
    }

    function TotalSalario()
    {

       
        $this->total_sueldo_bruto =0;
		$Conn = new bd($_SESSION[bd]);
        $query              = "SELECT ROUND(monto,2) as monto FROM nom_movimientos_nomina WHERE tipnom = '".$this->tipnom."' AND codnom = '".$this->codnom."' AND codcon=100";
        $result_salario     =  $Conn->query($query);
        while($fetch2       = $result_salario->fetch_array())
        {
            $sueldo_bruto       = $fetch2['monto'];
            $this->total_sueldo_bruto += $sueldo_bruto;
        }
        return number_format($this->total_sueldo_bruto, 2, '.',',');
    }
public function Header() {
		// Logo
		$Conn            = conexion();
		$var_sql         = "SELECT * from nomempresa";
		$rs              = query($var_sql,$Conn);
		$row_rs          = fetch_array($rs);
		$this->nomemp = utf8_encode($row_rs['nom_emp']);
		$var_encabezado1 = utf8_encode($row_rs['nom_emp']);
		$var_izquierda   = '../../imagenes/'.$row_rs[imagen_der];
		$var_derecha     = '../../imagenes/'.$row_rs[imagen_izq];
		$Conn            = conexion();

		$var_quincena    = "SELECT ntn.codtip, ntn.descrip as descripcion,nnp.fechapago,nnp.descrip , nnp.periodo_ini, nnp.periodo_fin
							from nomtipos_nomina ntn
							inner join nom_nominas_pago nnp on (ntn.codtip = nnp.tipnom)
							WHERE nnp.codnom = '".$this->codnom."' AND nnp.codtip = '".$this->tipnom."'";
		$rs_quince       = query($var_quincena,$Conn);
		$row_rs_quince   = fetch_array($rs_quince);
		$periodos = explode("-",$row_rs_quince['descrip']);
		$nombre_planilla = $row_rs_quince['descripcion']. " No. ".$this->codnom;
		$fechapago       = date('d/m/Y',strtotime($row_rs_quince['fechapago']));
		$periodo_ini       = date('d/M/Y',strtotime($row_rs_quince['periodo_ini']));
		$periodo_fin       = date('d/M/Y',strtotime($row_rs_quince['periodo_fin']));
		$codigo          = str_pad($row_rs_quince['codtip'], 3, "0", STR_PAD_LEFT);
		$Planilla  = "Control # ".$codigo ;
		$Periodo = $periodos[1]." ".$periodos[2]." ".$periodos[3]." ";

		$image_file = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 16);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$fill              = 1;
		$border            = "LTRB";
		$ln                = 0;
		$fill              = 0;
		$align             = 'C';
		$link              = 0;
		$stretch           = 1;
		$ignore_min_height = 0;
		$calign            = 'T';
		$valign            = 'T';
		$height            = 5.75;//alto de cada columna
		$YMax              = 260;
		$altura            = 21;
		//$Planilla .= "</b>";
		$membrete="<b>".$var_encabezado1."<br>";
					$membrete.= $nombre_planilla."<br>";
					$membrete.= $Periodo."<br>";
					$membrete .="</b>"; 
					;
        $this->Image($var_izquierda,11,11,19,19); 

		$altura2            = 6;

		$this->MultiCell(20, $altura,"Control #", $border="0", $align='C', $fill=false, $ln=1, $x=5,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(150, $altura,$membrete, $border="0", $align='C', $fill=false, $ln=1,$x=60	,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura2,'Ref: MM-701', $border="0", $align='l', $fill=false, $ln=1, $x=222 ,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura2,'Formato Detallado', $border="0", $align='l', $fill=false, $ln=1, $x=222 ,$y=16, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura2,'Aprobada', $border="0", $align='l', $fill=false, $ln=1, $x=222 ,$y=22, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		//$pdf->Rect(145, 10, 40, 20, 'D', array('all' => $style3));
		//$this->MultiCell(42, $altura2,$fechapago, $border="0", $align='l', $fill=false, $ln=1, $x=240 ,$y=16, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		//$this->MultiCell(42, $altura2,'Usuario: '.$this->getUser(), $border="0", $align='l', $fill=false, $ln=1, $x=240 ,$y=22, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->Ln();
	}

    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
	  //$buena= $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha

		$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
	  }
	}
	public function CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro)
	{
		$conexion=conexion();
		$fill              = 1;
		$border            = 1;
		$ln                = 0;
		$fill              = 0;
		$align             ='C';
		$link              = 0;
		$stretch           = 1;
		$ignore_min_height = 0;
		$calign            = 'T';
		$valign            = 'T';
		$height            = 5.75;//alto de cada columna
		$YMax              = 260;
		$altura            = 7;
		$sql_nv1       = "SELECT max(a.codnivel1) as codnivel1
					FROM nom_movimientos_nomina AS a
					WHERE a.tipnom = '".$this->tipnom."' AND a.codnom = '".$this->codnom."'";
		$bd           = new bd($_SESSION[bd]);
		$result_nv1 = $bd->query($sql_nv1);
		$num_columnas = $result_nv1-> num_rows;
		$iyy=0;
		$nv1Temp = 0;
		$numnv1 = 0;
        while ($fila_nv1 = $result_nv1->fetch_array())
	    {
	    	$partida = $fila_nv1[codnivel1];

		    $nv1Temp = $fila_nv1 ['codnivel1'];
			if($nv1Temp != $partidaNew AND $numnv1!=0){
				$nv1Temp = $fila_nv1 ['codnivel1'];
				$this->AddPage();
			}
			$numnv1++;
			/*Si es la primera pagina*/
		    if($this->GetY() <= 30)
			{
				$Y=28;
				$this->SetTopMargin ($Y);
				$this->SetFont('helvetica', '', 8);
			}
			$sql1              = " 
				SELECT DISTINCT b.ficha,b.apenom,b.cedula,b.seguro_social,b.nomposicion_id,
							b.forcob,b.suesal,b.cuentacob,b.suesal, b.hora_base,
							b.cta_presupuestaria as partida, c.des_car as cargo
	            FROM nom_movimientos_nomina AS a 
	            LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
	            LEFT JOIN nomcargos AS c ON c.cod_cargo = b.codcargo 
				WHERE a.codnom ='".$this->codnom."' AND a.tipnom='".$this->tipnom."' 
	            GROUP BY b.ficha 
				ORDER BY b.cta_presupuestaria, b.nomposicion_id";
			$conexion = new bd($_SESSION[bd]);
			$res1=$conexion->query($sql1);
			$cant_emp = $res1->num_rows;
			$this->Ln(6); 
			$this->SetFont('helvetica', '', 8);
			/*Busca registos*/
		    while($fila=$res1->fetch_array())
		    {
				$partidaNew=$fila_partida[partida];
		    	$this->total_descuentos = 0;
				//$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');
				/*Si es una página nueva, dibuja la cabecera*/
			    if($this->GetY() <= 25)
				{
					$Y=28;	        
					$this->SetFont('helvetica', '', 8);
				}

				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				// Header
				$num_headers = count($header);
				/*Si alcan el margen inferior, se hace agrega una página nueva con la cabecera*/

				if(	$this->GetY() >174)
				{
					$this->AddPage();
					$Y=28;
					$this->SetTopMargin ($Y);
					$this->SetFont('helvetica', '', 8);

				}
				
				$border=1;
				$ixx=0;
				if ($fila['forcob']=="Cheque") {
					$forcob="CH";
				}
				else {
					$forcob="ACH";
				}
			    $ficha = $fila['ficha'];
			    $bd = new bd($_SESSION[bd]);
			    $sql_montos = "SELECT 
			    (SELECT monto from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and codcon = 100 and ficha = '$ficha') as sueldo,
				(SELECT monto from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and codcon = 200 and ficha = '$ficha') as ss,
				(SELECT monto from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and codcon = 201 and ficha = '$ficha') as se,
				(SELECT monto from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and codcon = 202 and ficha = '$ficha') as isr,
				(SELECT monto from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and codcon = 209 and ficha = '$ficha') as siacap,
				(SELECT group_concat(codcon) as ot from nom_movimientos_nomina where codnom='".$this->codnom."' and tipnom='".$this->tipnom."' and (codcon >= 500 OR codcon in (210,211)) and ficha = '$ficha' and tipcon='D') as ot";
				$result_monto = $bd->query($sql_montos);
				$montos = $result_monto->fetch_array();
							$border='T';
				$acreedores = explode(',', $montos['ot']);
				$this->setTotalAcreedores($montos['ot']);

			    $bd = new bd($_SESSION[bd]);
				/*$sql_chequeFuncionario = "SELECT cheque from nomcheques 
				where  codnom='".$this->codnom."' and tipnom='".$this->tipnom."' AND cedula_rif = '".$fila['cedula']."'";
				$result_cheque = $bd->query($sql_chequeFuncionario);
				$cheque = $result_cheque->fetch_array();*/
				$cheque = '';
				$ancho_colab= array(30,60,30,60,30,16,16,16,16,16,16,16,16,16);
				$totalAcreedorFuncionario = $this->getTotalAcreedores();
				$this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 3, 'color' => array(0, 0, 0)));
				$this->SetFillColor(255,255,128);
				$wi=0;
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(60, $altura, utf8_decode($fila['apenom']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(60, $altura, $fila['codcargo'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(90, $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(40, $altura, "Num. Empleado"." ".$fila['ficha'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$wi=0;
				$this->SetFont('helvetica', '', 8);
				$this->ln();
				$border='0';
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell($ancho_colab[$wi++], $altura, "Horas", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "Ingresos", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "Monto", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "Retenciones", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "Monto", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(40, $altura, ("Cédula No.").": ".$fila['cedula'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->SetFont('helvetica', '', 8);
				$wi=0;
				$this->ln();
				$this->Cell($ancho_colab[$wi++], $altura, $fila['hora_base'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "Sueldo Base", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura,  number_format($montos['sueldo'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "200 Seguro Social", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, number_format($montos['ss'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(40, $altura, utf8_decode("Clave ISR").": ".$fila['clave_ir'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->SetFont('helvetica', '', 8);
				$wi=0;
				$this->ln();
				$this->Cell($ancho_colab[$wi++], $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, " ", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, "201 Seguro Educativo", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($ancho_colab[$wi++], $altura, number_format($montos['se'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(40, $altura, utf8_decode("Tarifa x hora").": ".$fila['clave_ir'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->SetFont('helvetica', '', 8);
				$wi=0;
				$islr = "";
				$this->ln();
				if($montos['isr']>0)
				{
					$islr = number_format($montos['isr'], 2, '.',',');
					$this->Cell(120, $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->Cell(60, $altura, "202 Impuesto Sobre La Renta", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->Cell(30, $altura, number_format($montos['isr'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->SetFont('helvetica', 'B', 8);
					$this->Cell(40, $altura, utf8_decode("Cuenta No.").": ".$fila['cuentacob'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->SetFont('helvetica', '', 8);
				}else{
					$this->Cell(210, $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->SetFont('helvetica', 'B', 8);
					$this->Cell(40, $altura, utf8_decode("Cuenta No.").": ".$fila['cuentacob'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->SetFont('helvetica', '', 8);

				}
				$wi=0;
				if($montos['siacap']>0)
				{
					$this->ln();					
					$islr = number_format($montos['isr'], 2, '.',',');
					$this->Cell(120, $altura, "", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->Cell(60, $altura, "209 SIACAP", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					$this->Cell(30, $altura, number_format($montos['siacap'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				}
				$this->total_descuentos += $montos['isr'];
				$this->total_descuentos += $montos['ss'];
				$this->total_descuentos += $montos['se'];			
				/*$this->Cell($w_cabecera[$ixx++], $altura, $fila['nomposicion_id'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, $fila['cedula'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, utf8_encode($fila['apenom']), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, $forcob, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, '', $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($montos['sueldo'], 2, '.',','), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($montos['isr'], 2, '.',','), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($montos['ss'], 2, '.',','), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura,number_format($montos['se'], 2, '.',','), $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($montos['siacap'], 2, '.',','), $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

				$this->Cell($w_cabecera[$ixx++], $altura, number_format($totalAcreedorFuncionario, 2, '.',','), $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($this->total_descuentos, 2, '.',','), $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, number_format($this->getTotalNetoFuncionario($montos['sueldo']), 2, '.',','), $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell($w_cabecera[$ixx++], $altura, $cheque['cheque'], $border,$ln,'R',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);*/
				$this->Ln(6); 
					$this->SetFont('helvetica', '', 8);

				if(	$this->GetY() >174)
				{
					$this->AddPage();
				}
			    if($this->GetY() <= 25)
				{
					$Y=28;
					$this->SetTopMargin ($Y); 
					$this->SetFont('helvetica', '', 8);
				}
				//$ancho = array(15,20, 70,20,20,30,30,40,30);
				$acreedores = explode(',', $montos['ot']);
				$border='0';

				for ($i=0; $i < count($acreedores); $i++) 
				{
					if(	$this->GetY() >174)
					{
						$this->AddPage();
					}
				    if($this->GetY() <= 25)
					{
						$Y=28;
						$this->SetTopMargin ($Y);   
						$this->SetFont('helvetica', '', 8);
					}
					$sql_acreedores = "SELECT nmn.descrip,nmn.monto, nmn.codcon
					from nom_movimientos_nomina nmn
					WHERE nmn.codcon = '{$acreedores[$i]}' AND nmn.codnom = '$this->codnom' AND nmn.tipnom='$this->tipnom' AND nmn.ficha ='$ficha'";
			    	$conex = new bd($_SESSION[bd]);
					$result_acreedor = $conex->query($sql_acreedores);
					$acreedor = $result_acreedor->fetch_assoc();
					if($result_acreedor->num_rows>0)
					{
						$ancho = array(120,60,60,60,20,20,20);
						$yxx=0;
						$this->Cell($ancho[$yxx++], $altura, '', $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
						$this->Cell($ancho[$yxx++], $altura, $acreedor['codcon']." ".$acreedor['descrip'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
						$this->Cell($ancho[$yxx++], $altura, $acreedor['monto'], $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					    $this->Ln(6); 
					    $this->total_descuentos += $acreedor['monto'];

					}
				    if(	$this->GetY() >174)
					{
						$this->AddPage();
					}

				    if($this->GetY() <= 25)
					{
						$Y=28;
						$this->SetTopMargin ($Y);
						$this->SetFont('helvetica', '', 8);
					}
				}
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(90, $altura, "Ingresos", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(30, $altura, number_format($montos['sueldo'], 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(60, $altura, "Retenciones", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(30, $altura, number_format($this->total_descuentos, 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				$this->Cell(60, $altura, utf8_decode("Neto a Pagar"). ": ".number_format($this->getTotalNetoFuncionario($montos['sueldo']), 2, '.',','), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
				
				$this->SetFont('helvetica', '', 8);
				$this->ln();
	         }//fin del while
			if(	$this->GetY() >174)
			{
				$this->AddPage();
			}
	    }

	    $columna_asig = $columna_deduc = $columna_patron = "";
	    $sum_asig = $sum_ret = 0;
	    $SQL1 = "SELECT nmn.codcon, nc.descrip, (SELECT sum(n.monto) from nom_movimientos_nomina n WHERE  n.codcon = nmn.codcon AND n.codnom='".$this->codnom."' and n.tipnom='".$this->tipnom."' and n.tipcon = 'A') as monto 
				from nom_movimientos_nomina nmn 
				INNER JOIN nomconceptos nc on (nc.codcon = nmn.codcon)  where nmn.codnom='".$this->codnom."' and nmn.tipnom='".$this->tipnom."' and nmn.tipcon = 'A' GROUP BY nmn.codcon";
		$conex = new bd($_SESSION[bd]);
		$result_asig = $conex->query($SQL1);

		$columna_asig .= '<table>';
		$columna_asig .= '<tr><td width="15%">Horas</td><td width="85%">INGRESOS</td></tr>';
		while($row = $result_asig->fetch_assoc()){
			$columna_asig .= '<tr><td>'.$row[codcon].'</td><td>'.$row[descrip].'</td></tr>';
			$sum_asig += $row[monto];
		}
		$columna_asig .= "<table>";
		
	    $SQL2 = "SELECT nmn.codcon, nc.descrip, (SELECT sum(n.monto) from nom_movimientos_nomina n WHERE  n.codcon = nmn.codcon AND n.codnom='".$this->codnom."' and n.tipnom='".$this->tipnom."' and n.tipcon = 'D') as monto 
				from nom_movimientos_nomina nmn 
				INNER JOIN nomconceptos nc on (nc.codcon = nmn.codcon)  where nmn.codnom='".$this->codnom."' and nmn.tipnom='".$this->tipnom."' and nmn.tipcon = 'D' GROUP BY nmn.codcon";
		$conex = new bd($_SESSION[bd]);
		$result_deduc = $conex->query($SQL2);

		$columna_deduc .= '<table>';
		$columna_deduc .= '<tr><td colspan="2">Retenciones</td></tr>';
		while($row = $result_deduc->fetch_assoc()){
			$columna_deduc .= '<tr><td width="15%">'.$row[codcon].'</td><td width="85%">'.$row[descrip].'</td></tr>';
			$sum_ret += $row[monto];
		}
		$columna_deduc .= "<table>";
		$neto_planilla = $sum_asig - $sum_ret;

		
	    $SQL3 = "SELECT nmn.codcon, nc.descrip, (SELECT sum(n.monto) from nom_movimientos_nomina n WHERE  n.codcon = nmn.codcon AND n.codnom='".$this->codnom."' and n.tipnom='".$this->tipnom."' and n.tipcon = 'P') as monto 
				from nom_movimientos_nomina nmn 
				INNER JOIN nomconceptos nc on (nc.codcon = nmn.codcon)  where nmn.codnom='".$this->codnom."' and nmn.tipnom='".$this->tipnom."' and nmn.tipcon = 'P' GROUP BY nmn.codcon";
		$conex = new bd($_SESSION[bd]);
		$result_patron = $conex->query($SQL3);	

		$columna_patron .= '<table>';
		$columna_patron .= '<tr><td width="15%">Concepto</td><td width="70%">Descrip.</td><td width="15%">Monto</td></tr>';
		while($row = $result_deduc->fetch_assoc()){
			$columna_patron .= '<tr><td>'.$row[codcon].'</td><td>'.$row[descrip].'</td><td>'.$row[monto];
			$columna_patron .= ' </td></tr>';
		}
		$columna_patron .= "<table>";
		
	    //página para totales acumulados
	    $this->AddPage();
	    $y = 30;


		$this->SetFont('helvetica', 'B', 8);
		$this->Cell(180, $altura, "Total: ".strtoupper($this->nomemp), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, $cant_emp. " empleados", $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->ln(5);
		$this->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
		$this->ln(5);

		$y = $this->GetY();
		// set color for background
		$this->SetFillColor(255, 255, 255);

		// set color for text
		$this->SetTextColor(0, 0, 0);

		// write the first column
		$this->writeHTMLCell(90, '', '', $y, $columna_asig, 1, 0, 1, true, 'J', true);

		// set color for background
		$this->SetFillColor(255, 255, 255);

		// set color for text
		$this->SetTextColor(0, 0, 0);

		// write the second column
		$this->writeHTMLCell(90, '', '', '', $columna_deduc, 1, 1, 1, true, 'J', true);

		// set color for background
		$this->SetFillColor(255, 255, 255);

		// set color for text
		$this->SetTextColor(0, 0, 0);
		$this->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

		$y = $this->GetY();
		$this->SetY($y);
		$this->ln(5);
		$this->Cell(90, $altura, "Total Ingresos: ".$sum_asig, $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(90, $altura, "Total Retenciones: ".$sum_ret, $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(60, $altura, "NETO A PAGAR: ".$neto_planilla , $border,1,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->ln(5);
		$this->Cell(120, $altura, "CONTROL DE EMPLEADOS", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", 0,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(120, $altura, "CONTROL DE PLANILLA", 1,1,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Activos", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Vacaciones", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Licencias", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Total", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", 0,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Efectivo", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Cheques", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Transferencias", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Total", 1,1,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		/**/

		$this->Cell(30, $altura, "", "LR",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LR",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LR",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LR",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", 0,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "1",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "1",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "1",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "1",1,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		/**/
		$this->Cell(30, $altura, "", "LRB",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LRB",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LRB",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", "LRB",$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", 0,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", 1,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", 1,1,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		/**/
		$this->ln(15);
		$this->Cell(120, $altura, "Elaborado por", 'T',$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", 0,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(120, $altura, "Aprobado por", 'T',$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->ln(5);

		// write the second column
		//$this->writeHTMLCell(90, '', '', '', $columna_patron, 1, 0, 1, true, 'J', true);

		// reset pointer to the last page
		$this->lastPage();

	}
public function Footer() {
		// Logo
		// Page number
		
	}
}//fin clase
		
		
		

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio=$_REQUEST['fecha_ini'];
$pdf->fechafinal=$_REQUEST['fecha_fin'];
$pdf->fecharegistro=$_REQUEST['fecha_reg'];


$pdf->codnom = $_REQUEST['nomina_id'];
$pdf->tipnom = $_REQUEST['codt'];
$pdf->NetoTotal();
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AMAXONIA');
$pdf->SetTitle('LISTADO PLANILLA DETALLADA');
$pdf->SetSubject('LISTADO PLANILLA DETALLADA');
$pdf->SetKeywords('TCPDF, PDF, REPORTE, CAMBIO, CEDULA');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10,true);
$pdf->SetLeftMargin(10);
$pdf->SetHeaderMargin(10);
$pdf->SetRightMargin(10);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15	);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('L', 'A4');

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------
ob_clean();
//Close and output PDF document
$pdf->Output('Listado_Planilla_Detallada.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();