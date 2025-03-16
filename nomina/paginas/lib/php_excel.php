<?php

function cellColor($cells,$color)
{
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)));
}

function colorTexto($celdas, $color_hex)
{
	global $objPHPExcel;

	$objPHPExcel->getActiveSheet()->getStyle($celdas)->applyFromArray(array('font'=>array('color'=>array('rgb'=>$color_hex))));
}

function allBordersMedium()
{
	$style = array('borders'=>array(
			'allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_MEDIUM) 
	));
	return $style;
}

function allBordersThin()
{
	$style = array('borders'=>array(
			'allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_THIN) 
	));
	return $style;
}

function allBorders( $tipo = PHPExcel_Style_Border::BORDER_THIN )
{
	$style = array( 'borders' => array(
						'allborders'=>array(
								'style'=> $tipo
						) 
			 ));
	return $style;	
}

function borderBottom( $tipo = PHPExcel_Style_Border::BORDER_THIN )
{
	$style = array(
		'borders' => array(
		    'bottom' => array(
		         'style' => $tipo
		    )
	));
	return $style;
}

function borderExterno( $tipo = PHPExcel_Style_Border::BORDER_THIN  )
{
	$style = array(
	  'borders' => array(
	    'outline' => array(
	      'style' => $tipo
	    )
	  ));	
	return $style;
}

function borderLeft( $tipo = PHPExcel_Style_Border::BORDER_THIN )
{
	$style = array(
		'borders' => array(
		    'left' => array(
		         'style' => $tipo
		    )
	));
	return $style;
}

function borderRight( $tipo = PHPExcel_Style_Border::BORDER_THIN )
{
	$style = array(
		'borders' => array(
		    'right' => array(
		         'style' => $tipo
		    )
	));
	return $style;
}

function borderTop( $tipo = PHPExcel_Style_Border::BORDER_THIN )
{
	$style = array(
		'borders' => array(
		    'top' => array(
		         'style' => $tipo
		    )
	));
	return $style;
}

function colorFondoLibro($color='FFFFFF')
{
	global $objPHPExcel;

	$objPHPExcel->getDefaultStyle()->applyFromArray(
	    array(
	        'fill' => array(
	            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => $color)
	        ),
	    )
	);
}

/*
* Cambiar formato de fecha de YYYY/MM/DD a DD/MM/YYYY
*/

function fecha($value, $separador='/') 
{ 
  if($separador!='/' && $separador!='-')
  {
  		$separador='/';
  } 
  		
  if ( ! empty($value) )
  {
  		return substr($value,8,2) . $separador . substr($value,5,2) . $separador . substr($value,0,4);
  } 
}

function recortar_numero ($numero, $decimales)
{
    $parte = explode (".", $numero);
    $despues_coma = substr($parte[1], 0, $decimales-1);
    return $parte[0].".".$despues_coma;
}