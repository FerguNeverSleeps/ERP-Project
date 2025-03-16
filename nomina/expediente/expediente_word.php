<?php
	
        
	$cedula=$_GET['cedula'];
        $codigo=$_GET['codigo'];
	$opcion=$_GET['tipo'];
        
        
        //echo $opcion;
	switch ($opcion)
	{	
               
                case '52':                   
                    
                    include 'word/cese_labores_word.php'; 
                case '57':                   
                    
                    include 'word/cese_labores_word.php'; 
                        
		break;
                
	}
        
        
?>


