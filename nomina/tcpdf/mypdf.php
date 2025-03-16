<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

require('tcpdf.php');
require_once('../fpdi/fpdi.php');
require_once('../lib/config.php'); // general.config.inc.php
//require_once('../lib/common.php'); // funciones
require_once('../lib/common_excel.php'); 
include('../fpdf/numerosALetras.class.php');
include ("../paginas/func_bd.php");
include ("../paginas/funciones_nomina.php"); 

class MYPDF extends FPDI
{
    var $_tplIdx;
	
    public function Header() 
    {
    	$conexion=conexion();

        // Lo primero que se debe hacer es consultar las opciones del modelo de constancia
        // Para saber si se va a usar el header y pie de página de la configuración general
        // o si se va a usar un template o si no se desea usar ninguno de los dos

        $res = query('SELECT configuracion, template 
                      FROM   nomtipos_constancia 
                      WHERE  codigo='. $GLOBALS['constancia_id'], $conexion);
        $fila = fetch_array($res);
        $configuracion = utf8_encode($fila['configuracion']);

        if($configuracion == 'Template')
        {
            if (is_null($this->_tplIdx)) 
            {
                $template = "templates/".$fila['template'];
                if (file_exists($template)) 
                {
                    $this->setSourceFile($template);
                    $this->_tplIdx = $this->importPage(1);
                    $size = $this->useTemplate($this->_tplIdx);
                }
            }    
        }

        if($configuracion == 'Header')
        {
		    	$res=query('SELECT encabezado FROM nomconf_constancia', $conexion);
		    	$fila=fetch_array($res);
		    	$encabezado=$fila['encabezado'];
		         
		    	if(!empty($encabezado))
		    	{
		    		//$this->SetFont('helvetica', 'B', 8);
		    		$this->SetTopMargin(0);
	    			$this->SetLeftMargin(0);
	    			$this->SetRightMargin(0);
		    		//$this->SetY(5);
		    		$encabezado=str_replace('"', "'", $encabezado);
		    		eval("\$html = \"$encabezado\";");
		    		$html=str_replace("'", '"', $html);
		    		$this->writeHTML($html, true, false, true, false, '');
		    	}
        }        
    }

    public function Footer()
    {
    	$conexion = conexion();

        $res = query('SELECT configuracion 
                      FROM   nomtipos_constancia 
                      WHERE  codigo='. $GLOBALS['constancia_id'], $conexion);
        $fila = fetch_array($res);
        $configuracion = utf8_encode($fila['configuracion']);

        if($configuracion == 'Header')
        {        
	    	$res=query('SELECT pie_pagina FROM nomconf_constancia', $conexion);
	    	$fila=fetch_array($res);
	    	$pie_pagina=$fila['pie_pagina'];

	    	if(!empty($pie_pagina))
	    	{
	    		$this->SetY(-43); // $pdf->SetFooterMargin(43);
	    		//$this->SetFont('helvetica','',9);
	    		$this->SetLeftMargin(0);
	    		$this->SetRightMargin(0);
	    		$pie_pagina=str_replace('"', "'", $pie_pagina);
	    		eval("\$html = \"$pie_pagina\";");
	    		$html=str_replace("'", '"', $html);
	    		$this->writeHTML($html, true, false, true, false, '');
	    	}
	    }        
    }
}
?>