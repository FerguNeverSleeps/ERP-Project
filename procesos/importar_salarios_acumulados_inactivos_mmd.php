<?php
include('../generalp.config.inc.php');
session_start();

error_reporting(E_ALL);

$db_user   = DB_USUARIO;
$db_pass   = DB_CLAVE;
$db_name   = $_SESSION['bd'];
$db_host   = DB_HOST;
$usuario = $_SESSION['usuario'];

$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

    //CASO AUTOMATICO DIARIO
    //$fecha_registro = date('Y-m-d');   
    //CASO MANUAL DIARIO
    $fecha_registro = "2017-02-28";
    $anio_registro = date("Y", strtotime($fecha_registro));
    $mes_registro = date("m", strtotime($fecha_registro));
    $dia_registro = date("d", strtotime($fecha_registro));

$consulta_acumulados="SELECT * FROM `data_acumulados_inactivos` "
        . "ORDER BY `data_acumulados_inactivos`.`Cedula` ASC,`data_acumulados_inactivos`.`Fecha_Pago` ASC,`data_acumulados_inactivos`.`NUMERO_NOMINA` ASC";

$resultado_acumulados=mysqli_query($conexion,$consulta_acumulados);

$cont=1;
while($fila=mysqli_fetch_array($resultado_acumulados))
{          
   $salario_bruto=$vacac=$xiii=$gtorep=$xiii_gtorep=$liquida=$bono=$otros_ing=$prima=$s_s=$s_e=$islr=$islr_gr=$acreedor_suma=$Neto=0.0;
    
    $NUM=trim($fila['NUM']);
    $EMPLEADO=trim($fila['EMPLEADO']);
    $ESTADO_EMPLEADO=trim($fila['ESTADO_EMPLEADO']);
    $CONCEPTO_SB=trim($fila['CONCEPTO_SB']);
    $TOTAL=trim($fila['TOTAL']);
    if($TOTAL<0)
    {
        $TOTAL=$TOTAL*(-1);
    }
    $NOMINA=trim($fila['NOMINA']);
    $NUMERO_NOMINA=trim($fila['NUMERO_NOMINA']);
    $CENTRO_COSTO=trim($fila['CENTRO_COSTO']);
    $Fecha_Pago=trim($fila['Fecha_Pago']);
    $Cedula=trim($fila['Cedula']);
    
    echo "NUM: "; echo $NUM;
    echo " - CEDULA: "; echo $Cedula;
    echo " - EMPLEADO: "; echo $EMPLEADO;
    echo " - NOMINA: "; echo $NOMINA;
    echo " - NUMERO_NOMINA: "; echo $NUMERO_NOMINA;
    echo " - CONCEPTO_SB: "; echo $CONCEPTO_SB;
    echo " - FECHA PAGO: "; echo $Fecha_Pago;   
    echo " - MONTO: "; echo $TOTAL;    
    echo "<br>";
    
    $query="SELECT * FROM data_acumulados_inactivos WHERE cedula='$Cedula' AND fecha_pago='$Fecha_Pago'";	
    $resultado=mysqli_query($conexion,$query);
    $row=mysqli_fetch_array($resultado);
    $id=$row[id];
    
    if($NOMINA=='1' || $NOMINA=='2' || $NOMINA=='ACM')
    {
        if($CONCEPTO_SB=='SQ0001')
        {
            $salario_bruto=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0054')
        {
            $gtorep=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
        {
            $prima=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL003')
        {
            $islr=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL004')
        {
            $islr_gr=$TOTAL;
        }
        
    }
    
    if($NOMINA=='DEC')
    {
        if($CONCEPTO_SB=='SQ0001')
        {
            $xiii=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0054')
        {
            $xiii_gtorep=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
        {
            $prima=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL003')
        {
            $islr=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL004')
        {
            $islr_gr=$TOTAL;
        }
        
    }
    
    if($NOMINA=='VAC')
    {
        if($CONCEPTO_SB=='SQ0001')
        {
            $vacac=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0054')
        {
            $gtorep=$TOTAL;
        }
        
        if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
        {
            $prima=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL003')
        {
            $islr=$TOTAL;
        }
        
        if($CONCEPTO_SB=='DQL004')
        {
            $islr_gr=$TOTAL;
        }
        
    }
    
    if ($id===NULL || $id===0 || $id==='' || $id===' ') 
    {// Si el registro_id es 0 se va a agregar un registro nuevo
		
		
		$query="INSERT INTO salarios_acumulados 
                        (id,
                        ficha,
                        cedula,
                        fecha_pago,
                        cod_planilla,
                        tipo_planilla,
                        frecuencia_planilla,
                        salario_bruto,
                        vacac,
                        xiii,
                        gtorep,
                        xiii_gtorep,
                        liquida,
                        bono,
                        otros_ing,
                        prima,
                        s_s,
                        s_e,
                        islr,
                        islr_gr,
                        acreedor_suma,
                        Neto)
                        values 
                        ('',
                        '$EMPLEADO',"
                        . "'$Cedula',"
                        . "'$Fecha_Pago',"
                        . "'$NUMERO_NOMINA',"
                        . "'1',"
                        . "'$frecuencia_planilla',"
                        . "'$salario_bruto',"
                        . "'$vacac',"
                        . "'$xiii',"
                        . "'$gtorep',"
                        . "'$xiii_gtorep',"
                        . "'$liquida',"
                        . "'$bono',"
                        . "'$otros_ing',"
                        . "'$prima',"
                        . "'$s_s',"
                        . "'$s_e',"
                        . "'$islr',"
                        . "'$islr_gr',"
                        . "'$acreedor_suma',"
                        . "'$Neto')";
		
		
                $resultado=mysqli_query($conexion,$query);
		
	}
	else 
        {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
//					
//		$query="UPDATE salarios_acumulados SET
//                        salario_bruto='$salario_bruto',
//                        vacac='$vacac',
//                        xiii='$xiii',	
//                        gtorep='$gtorep',
//                        xiii_gtorep='$xiii_gtorep',
//                        liquida='$liquida',
//                        bono='$bono',
//                        otros_ing='$otros_ing',
//                        prima='$prima',
//                        s_s='$s_s',
//                        s_e='$s_e',
//                        islr='$islr',
//                        islr_gr='$islr_gr',
//                        acreedor_suma='$acreedor_suma',
//                        Neto='$Neto'    
//                        where id='$id'";	
		//exit(0);
		
                
                if($NOMINA=='1' || $NOMINA=='2' || $NOMINA=='ACM')
                {
                    if($CONCEPTO_SB=='SQ0001')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                salario_bruto='$salario_bruto'
                                where id='$id'";	
                    }

                    if($CONCEPTO_SB=='BQ0054')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                gtorep='$gtorep'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                prima='$prima'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL003')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                islr='$islr'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL004')
                    {
                                               
                        $query="UPDATE salarios_acumulados SET
                                islr_gr='$islr_gr'
                                where id='$id'";
                    }

                }

                if($NOMINA=='DEC')
                {
                    if($CONCEPTO_SB=='SQ0001')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                xiii='$xiii'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='BQ0054')
                    {
                                                
                        $query="UPDATE salarios_acumulados SET
                                xiii_gtorep='$xiii_gtorep'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
                    {
                        
                        $query="UPDATE salarios_acumulados SET
                                prima='$prima'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL003')
                    {                                                
                        $query="UPDATE salarios_acumulados SET
                                islr='$islr'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL004')
                    {
                                                
                        $query="UPDATE salarios_acumulados SET
                                islr_gr='$islr_gr'
                                where id='$id'";
                    }

                }

                if($NOMINA=='VAC')
                {
                    if($CONCEPTO_SB=='SQ0001')
                    {
                        $query="UPDATE salarios_acumulados SET
                                vacac='$vacac'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='BQ0054')
                    {
                        $query="UPDATE salarios_acumulados SET
                                gtorep='$gtorep'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='BQ0082' || $CONCEPTO_SB=='BQ0084')
                    {                        
                        $query="UPDATE salarios_acumulados SET
                                prima='$prima'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL003')
                    {                        
                        $query="UPDATE salarios_acumulados SET
                                islr='$islr'
                                where id='$id'";
                    }

                    if($CONCEPTO_SB=='DQL004')
                    {                                                
                        $query="UPDATE salarios_acumulados SET
                                islr_gr='$islr_gr'
                                where id='$id'";
                    }

                }
                
                $resultado=mysqli_query($conexion,$query);
					
	}
    
    $cont++;
}

?>

