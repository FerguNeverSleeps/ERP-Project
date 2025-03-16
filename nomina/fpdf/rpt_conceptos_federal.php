<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];

require('fpdf.php');
include("../lib/common.php");
include("../lib/pdf.php");

class PDF extends FPDF
{
    var $codnom;
    var $tipnom;
    var $frecuencia;
    var $mes;
    var $anio;
    var $concepto;
    var $apenom;
    var $cedula;
    var $ficha;
    function header(){
    	$conexion= new bd($_SESSION['bd_nomina']);
    	$var_sql="select * from nomempresa";
    	$rs = $conexion->query($var_sql);
    	$row_rs = $rs->fetch_array();
    	$var_encabezado=$row_rs['nom_emp'];

        $codcon1=explode(',',$this->concepto);
        if(!empty($codcon1[0]))
        {
            $consulta="select * from nomconceptos WHERE codcon in ({$this->concepto})";
            $resultado33=$conexion->query($consulta);
            ;
            while($fetch33 = $resultado33->fetch_array())
            {
                $concep .=$fetch33[codcon].' - '.$fetch33[descrip];
            }

        }
    	
    	if($this->validarVariable($this->frecuencia)){
            $consulta="select descrip from nomfrecuencias WHERE codfre in ($this->frecuencia)";
            $resultado32=$conexion->query($consulta);
            while($fetch32 = $resultado32->fetch_array())
            {
                $frecc.=$fetch32[descrip].' ';
            }
    	}
    	if($this->validarVariable($this->tipnom)){
            $consulta="select descrip from nomtipos_nomina WHERE codtip in ($this->tipnom)";
            $resultado34=$conexion->query($consulta);
            while($fetch34 = $resultado34->fetch_array())
            {
                $nomm.=$fetch34['descrip'].' ';
            }
        }

    	$nomm=substr($nomm,0,-2);
    	$this->SetFont('Arial','',12);
    	$date1=date('d/m/Y');
    	$date2=date('h:i a');	

    	$this->Cell(150,5,utf8_decode($var_encabezado),0,0,'L');
    	$this->Cell(38,5,'Fecha:  '.$date1,0,1,'R');
        $this->Cell(150,5,'RECURSOS HUMANOS',0,0,'L');
        $this->ln();
    	$this->Cell(38,5,'',0,1,'R');
    	$this->Cell(150,5,$concep,0,1,'L');
        $this->SetFont('Arial','',10);
        $this->SetWidths(array(30,125,45,24,24));
        $this->SetAligns(array('L','L','L','R','R'));
        $this->Setceldas(array(0,0,0,0,0));
        $this->Setancho(array(5,5,5,5,5));        $this->ln();

        $this->Row(array('FICHA: '.utf8_decode($this->ficha),'APELLIDOS: '.utf8_decode($this->apenom),utf8_decode('CÉDULA: ').utf8_decode($this->cedula)));
        $this->ln();
    	
    	
    }
    function validarVariable($variable){
        if ($variable != "" or $variable != NULL){
            return true;
        }
        else{
            return false;
        }

    }

    function movimientos($codcon,$fecha_inicio,$fecha_fin,$ficha)
    {
    	$conexion= new bd($_SESSION['bd_nomina']);
        $consulta="";

        $SELECT = " SELECT nmn.codnom, nmn.codcon, nmn.ficha, nmn.valor, nmn.monto as monto, nmn.tipnom, nc.descrip,date_format(nmp.fechapago,'%d/%m/%Y') fecha ";

        $FROM = " FROM nom_movimientos_nomina nmn
        join nom_nominas_pago nmp on (nmn.codnom=nmp.codnom and nmn.tipnom=nmp.tipnom)
        join nomconceptos nc on (nc.codcon=nmn.codcon) ";

        
        $band1 = 0;
        $band2 = 0;
        $band3 = 0;
        $band4 = 0;
        if  ($this->validarVariable($fecha_inicio) AND $this->validarVariable($fecha_fin)){
            $FECHAS =  " nmp.fechapago BETWEEN  '{$fecha_inicio}' AND '{$fecha_fin}' ";
            $sw = 1;
            $band1 = 1;

        }
        if ($this->validarVariable($ficha)){
            $sw = 1;
            $FICHA = "";
            if($band1)
            {
                $FICHA .= " AND ";

            }
            $FICHA .= " nmn.ficha = '{$ficha}'";
            $band1 = 2;

        }
        if ($this->validarVariable($codcon)){
            $CONCON1 = "";
            
            if($band1 OR $band2 OR $band3)
            {
                $CONCON1 .= " AND ";

            }
            $CONCON1 .= "  nmn.codcon in ({$codcon}) ";
            $sw = 1;
            $band1 = 3;

        }
        if ($this->validarVariable($tipo)){
            $FREQ = "";
            
            if($band1 OR $band2 OR $band3)
            {
                $FREQ .= " AND ";

            }
            $FREQ .= "  nmp.tipnom in ({$tipo}) ";
            $sw = 1;
            $band1 = 3;

        }
        if ($sw == 1)
        {
            $WHERE .= " WHERE ";
            $WHERE .=  $FECHAS;
            $WHERE .=  $FICHA;
            $WHERE .=  $CONCON1;
            $WHERE .=  $FREQ;
        }

        $ORDER = " ORDER BY nmn.cedula ";

        $consulta .= $SELECT;
        $consulta .= $FROM;
        $consulta .= $WHERE;
        $consulta .= $GROUP;
        $consulta .= $ORDER;
    	$resultado=$conexion->query($consulta);
    	
    	$totalwhile=$resultado->num_rows;

    	$totalPrest=$totalSaldo=$total=0;
        $concepto = 0;
        if($totalwhile > 0)
        {    
            while($fila=$resultado->fetch_array())
            {
                $this->SetFont('Arial','',10);
                if($fila['monto']!=0)
                {
                    if($concepto == 0)
                    {
                        $concepto = 1;
                        $desc_con = $fila['descrip'];                        
                        $descripcion = $fila['descrip'];                        
                    }
                    else {
                        $desc_con = "";
                    }
                    $cedula=$persona['cedula'];
                    $this->SetFont('Arial','',9);
                    $this->SetWidths(array(14,65,65,24,24));
                    $this->SetAligns(array('C','L','L','R','R'));
                    $this->Setceldas(array(0,0,0,0,0));
                    $this->Setancho(array(5,5,5,5,5));
                    $this->Row(array("(".$fila['codcon'].")",$desc_con,$fila['fecha'],number_format($fila['valor'],2,'.',''),number_format($fila['monto'],2,'.','')));
                    $total+=$fila['monto'];
                }
            }
        }
        else
        {
            $this->SetFont('Arial','',9);
            $this->SetWidths(array(190));
            $this->SetAligns(array('L','R','L','R','R'));
            $this->Setceldas(array(1,0,0,0,0)); 
            $this->Setancho(array(5));
            $this->Row(array("No existen registro para mostrar"));
        }
    	$this->Ln(2);
    	
    	$this->SetFont('Arial','',10);
    	$this->Cell(177,5,'Total Concepto '.$descripcion,0,0,'R');
    	$this->Cell(15,5,number_format($total,2,',','.'),1,0,'R');	
    }

    function Footer()
    {

    	
    	$this->SetY(-20);
        	$this->SetFont('Arial','',8);
        	$this->Cell(0,5,'Elaborado Por: '.$_SESSION['nombre'],0,1,'L');
    	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo(),0,1,'C');
    	
    }
    function finalizar(){

    	$bool=validar_firma("PATRONALES");
    	if ($bool==true){
    	}else{
    		patronales($this->fpdf);
    	}
    }

}
$frecuencias=isset($_GET['frecuencias']) ? $_GET['frecuencias'] : '';
$conceptos=isset($_GET['conceptos']) ? $_GET['conceptos'] : '';
$planilla=isset($_GET['planilla']) ? $_GET['planilla'] : '';
$fecha_inicio=isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin=isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$ficha=isset($_GET['ficha']) ? $_GET['ficha'] : '';
$pdf=new PDF();
$pdf->conceptos = $conceptos;


 
$conex = new bd($_SESSION['bd_nomina']);
$select_fechas = "SELECT 
    nmn.ficha,nmn.codcon, np.apenom, np.ficha, np.cedula
FROM
    nom_nominas_pago nnp
INNER JOIN
    nom_movimientos_nomina nmn  ON nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom
INNER JOIN
    nompersonal np  ON np.ficha = nmn.ficha
WHERE
    nnp.fechapago BETWEEN  '{$fecha_inicio}' AND '{$fecha_fin}' ";
if($pdf->validarVariable($conceptos))
    $select_fechas .=  " AND nmn.codcon in ({$conceptos}) ";
if($pdf->validarVariable($ficha))
    $select_fechas .=  " AND nmn.ficha = '{$ficha}' ";
$select_fechas .= " 
GROUP BY 
    nmn.ficha,nmn.codcon ";
$select_fechas .= " 
ORDER BY 
    nmn.ficha DESC, nmn.codcon";
$result_fechas = $conex->query($select_fechas);

while($arrayPlanilla = $result_fechas->fetch_assoc()){
    $pdf->concepto = $arrayPlanilla['codcon'];
    $pdf->apenom = $arrayPlanilla['apenom'];
    $pdf->cedula = $arrayPlanilla['cedula'];
    $pdf->ficha = $arrayPlanilla['ficha'];
    $pdf->AddPage('P','Letter');
    $pdf->AddFont('Sanserif','','sanserif.php');
    $pdf->SetFont('Sanserif','',12);
    $pdf->movimientos($arrayPlanilla['codcon'],$fecha_inicio,$fecha_fin,$arrayPlanilla['ficha']);

}
$pdf->Output();
?>
