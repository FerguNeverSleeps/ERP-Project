<?php
require_once('../../lib/database.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

date_default_timezone_set('America/Panama'); // Usar un parámetro para saber la timezone

require('vendor/tcpdf/tcpdf.php');
require('vendor/fpdi/fpdi.php');

$db = new Database($_SESSION['bd']);

$t     = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$d     = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );

$CODIGO_VERIFICACION = date("jnyH"); //. substr((string) $d->format("u") , 1, 3);

class MYPDF extends FPDI
{
    var $_template;
	
    public function Header() 
    {
        global $constancia_id, $db, $CODIGO_VERIFICACION;

        /* Lo primero que se debe hacer es consultar las opciones del modelo de constancia   */
        /* para saber si se va a usar el header y pie de página de la configuración general, */
        /* si se va a usar un template o si no se desea usar ninguno de los dos              */

        $sql = "SELECT configuracion, template
                FROM   nomtipos_constancia
                WHERE  codigo='".$constancia_id."'";
        $res = $db->query($sql);
        $carta = $res->fetch_object();

        if($carta->configuracion == 'Template')
        {
            if (is_null($this->_template)) 
            {
                $template = "templates/" . $carta->template;

                if (file_exists($template)) 
                {
                    $this->setSourceFile($template);
                    $this->_template = $this->importPage(1);
                    $size = $this->useTemplate($this->_template);
                }
            }    
        }
        else if($carta->configuracion == 'Header')
        {
            $sql = "SELECT encabezado 
                    FROM   nomconf_constancia";
            $res = $db->query($sql);
            $header = $res->fetch_object()->encabezado;  

            if($header!='')
            {
                $this->SetTopMargin(0);
                $this->SetLeftMargin(0);
                $this->SetRightMargin(0);

                $header=str_replace('"', "'", $header);
                eval("\$html = \"$header\";");
                $html=str_replace("'", '"', $html);
                $this->writeHTML($html, true, false, true, false, '');                
            }         
        } 

        // Verificamos si se muestra el codigo de verificación

        $sql = "SELECT codigo_verificacion, COALESCE(posicionx_codigo, 155) as posicionx_codigo, 
                       COALESCE(posiciony_codigo, 15) as posiciony_codigo, texto_codigo
                FROM   nomconf_constancia";
        $res = $db->query($sql); 

        $conf = $res->fetch_object();

        if($conf->codigo_verificacion=='Si')
        {
            $this->SetXY($conf->posicionx_codigo, $conf->posiciony_codigo);

            //$this->Cell(25, 10, $CODIGO_VERIFICACION, 0, false, 'R', 0, '', 0, false, 'C', 'C'); 

            $html = str_replace('"', "'", $conf->texto_codigo);

            eval("\$html = \"$html\";");

            $html = str_replace("'", '"', $html);

            //$this->writeHTML($html, false, false, false, true, 'R');   
            $this->Multicell(40, 8, $html, 0, 'R', false, 1, '', '', true, 0, true, true, 0, 'T', false);
        }    
    }

    public function Footer()
    {
        global $constancia_id, $db;

        $sql = "SELECT configuracion
                FROM   nomtipos_constancia
                WHERE  codigo='".$constancia_id."'";
        $res = $db->query($sql);
        $carta = $res->fetch_object();

        if($carta->configuracion == 'Header')
        {
            $sql = "SELECT pie_pagina 
                    FROM   nomconf_constancia";
            $res = $db->query($sql);
            $footer = $res->fetch_object()->pie_pagina; 

            if($footer!='')
            {
                $this->SetY(-43);
                $this->SetLeftMargin(0);
                $this->SetRightMargin(0);
                $footer=str_replace('"', "'", $footer);
                eval("\$html = \"$footer\";");
                $html=str_replace("'", '"', $html);
                $this->writeHTML($html, true, false, true, false, '');
            }            
        }
    }
}
?>