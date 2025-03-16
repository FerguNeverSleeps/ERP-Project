<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
// | AUTOR        : ERNESTO RIVAS MARVAL                                    |
// +------------------------------------------------------------------------+
// | FECHA - HORA : 12/07/2017 - 11:36 PM                                   |
// +------------------------------------------------------------------------+
//
// $Id$
//


/**
* @author       Ernesto Rivas
*/
class Curriculum
{
    /**
    * @var      objeto
    */
    private $coDB;
    
    
    /**
    * @return   void
    */
    public function __construct($conexion)
    {
       // TODO: implement
	  $this->coDB = $conexion;
    }
    
        
    public function get_profesion()
    {               
        $sql    ="SELECT * FROM nomprofesiones ORDER BY nomprofesiones.descrip ASC";        
        //$rows   = $this->coDB->query($sql);
        $resp   = $this->coDB->fetch_all_array($sql);    
        $this->coDB->close();    
        //echo 'sd'.count($resp);
        return $resp;
    }

    public function get_instruccion(){
        $sql        ='SELECT * FROM nominstruccion ';
          //$rows   = $this->coDB->query($sql);
        $resp   = $this->coDB->fetch_all_array($sql);    
        $this->coDB->close();    
        //echo 'sd'.count($resp);
        return $resp;
    }

    public function get_desempeno(){
        $sql        ='SELECT * FROM nomdesempeno ';
          //$rows   = $this->coDB->query($sql);
        $resp   = $this->coDB->fetch_all_array($sql);    
        $this->coDB->close();    
        //echo 'sd'.count($resp);
        return $resp;
    }

    public function save_curriculum($atributos){
        $tabla                  = 'nomelegibles';  
        $atributos['fecnac']    = $this->fecha_formato($atributos['fecnac']); 
        $resp                   = $this->coDB->query_insert($tabla,$atributos);
        //echo 'aqui'.$resp.'-'.strlen($resp);
        $this->coDB->close();
        if($resp==1){
            $estructura = PATH.SYS.'adjuntos/'.$atributos['cedula'].'/';             
            // Para crear una estructura anidada se debe especificar
            // el parámetro $recursive en mkdir().
            if(!mkdir($estructura, 0777, true)) {
                die('Fallo al crear las carpetas...'.$estructura);
            } 
            else{
                if (move_uploaded_file($_FILES['file_foto']['tmp_name'], $estructura.$atributos['foto']) && move_uploaded_file($_FILES['file_documento']['tmp_name'], $estructura.$atributos['archivo']) ) {
                    //echo "El fichero es creado y se subió con éxito ";
                    return 1;
                } 
                else {
                    return -2;
                }
            }
        }
        else{
            return $resp;
        }                
    }

    public function fecha_formato($fecha){
        if ($fecha == "00/00/0000")
        {
            $fecha=date("Y-m-d");
        }else{
            $e = explode('/', $fecha);
            $fecha = $e[2]."-".$e[1]."-".$e[0];
        }
        return $fecha;
    }



	
// 
    
    

    
		
  
}

?>