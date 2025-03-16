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

   
    $fecha_registro = "2017-02-28";
    $anio_registro = date("Y", strtotime($fecha_registro));
    $mes_registro = date("m", strtotime($fecha_registro));
    $dia_registro = date("d", strtotime($fecha_registro));

$consulta_acumulados="SELECT * FROM `data_prestamos` "
        . "ORDER BY `data_prestamos`.`ID` ASC";

$resultado_acumulados=mysqli_query($conexion,$consulta_acumulados);

$cont=1;
while($fila=mysqli_fetch_array($resultado_acumulados))
{          
   
    $ID=trim($fila['ID']);
    $Ficha=trim($fila['Ficha']);
    $Acreedor=trim($fila['Acreedor']);
    $Nombre=trim($fila['Nombre']);
    $Tipo=trim($fila['Tipo']);
    $Acumulado=trim($fila['Acumulado']);
    $Cuota_mensual=trim($fila['Cuota']);
    $Cuota_quincenal=(trim($fila['Cuota']))/2;
    $Saldo=trim($fila['Saldo']);
    $acreedor_id=trim($fila['acreedor_id']);
    $tipo_id=trim($fila['tipo_id']);
    $descripcion=$Acreedor." - ".$Tipo;
    
    $monto_prestamo=$Acumulado+$Saldo;    
   
    $num_cuotas=round($monto_prestamo/$Cuota_quincenal);
    
    $num_cuotas_acumulado=round($Acumulado/$Cuota_quincenal);
    
    $num_cuotas_saldo=round($Saldo/$Cuota_quincenal);
    
    $estado='Pendiente';
        
    $fecha_ini="2020-08-31";
            
            
    echo "NUM: "; echo $ID;
    echo " - FICHA: "; echo $Ficha;
    echo " - NOMBRE: "; echo $Nombre;
    echo " - ACREEDOR: "; echo $Acreedor;
    echo " - TIPO: "; echo $Tipo;
    echo " - ACUMULADO: "; echo $Acumulado;
    echo " - CUOTA: "; echo $Cuota_mensual;   
    echo " - CUOTA QUICNENAL: "; echo $Cuota_quincenal;   
    echo " - SALDO: "; echo $Saldo;
    echo " - TOTAL: "; echo $monto_prestamo;    
    echo " - CUOTAS SALDO: "; echo $num_cuotas_saldo;  
    echo " - CUOTAS TOTAL: "; echo $num_cuotas; 
    echo " - ESTATUS: "; echo $estado;  
    echo "<br>"; 
    echo "<br>"; 
		
    $query="INSERT INTO nomprestamos_cabecera "
    . "SET numpre='".$ID."', "
    . "ficha='".$Ficha."', "
    . "fechaapro='".$fecha_ini."', "
    . "fecpricup='".date("Y-m-d",strtotime($fecha_ini."+ 15 days"))."', "
    . "monto='".$monto_prestamo."', "
    . "estadopre='".$estado."', "
    . "detalle='".$descripcion."', "
    . "codigopr='".$acreedor_id."', "
    . "codnom=1,"
    . "totpres='".$monto_prestamo."', "
    . "cuotas='".$num_cuotas."', "
    . "mtocuota='".$Cuota_mensual."', "
    . "diciembre='".$_POST[diciembre]."',"
    . "gastos_admon='0',"
    . "id_tipoprestamo='".$tipo_id."',"
    . "frededu='2',"
    . "monto_acumulado='".$Acumulado."'";

    $resultado=mysqli_query($conexion,$query);

//    if($Saldo!='' && $Saldo!=NULL && $Saldo!=0 && $Saldo!=' ')
//    {
    $salini=$Saldo;        
    $numcuo=$num_cuotas_acumulado+1;

    do
    {
        $salfin=$salini-$Cuota_quincenal;

        if($salfin<0)
        {
            $Cuota_quincenal=$salini;
            $salfin=0;
            
        }

        $fecha_cuota=date("Y-m-d",strtotime($fecha_ini."+ 15 days")); 

        $consulta="INSERT INTO nomprestamos_detalles "
                . "SET numpre='$ID', "
                . "ficha='$Ficha', "
                . "numcuo='".$numcuo."', "
                . "fechaven='".$fecha_cuota."', "
                . "anioven=".date("Y",strtotime($fecha_cuota)).", "
                . "mesven=".date("m",strtotime($fecha_cuota)).", "
                . "salinicial=".$salini.", "
                . "montocuo=".$Cuota_quincenal.", "
                . "salfinal=".$salfin.", "
                . "estadopre='Pendiente', "
                . "codnom=1";
//            echo "<br>"; 
//            exit;
        $resultado=mysqli_query($conexion,$consulta);

        $salini=$salfin;
        $fecha_ini=$fecha_cuota;
        $numcuo++;


    }while($salfin>0);
    
    $cont++;
}

?>

