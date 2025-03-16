<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

$tipo = $_GET['tipo'];
$cedula = $_GET['cedula'];
$editar = $_GET['editar'];
$codigo = $_GET['codigo'];

if($editar==1)
{


        $sql_expediente = "SELECT  periodo_vacacion
                FROM   expediente 
                WHERE  cod_expediente_det='{$codigo}'";

        $resultado_expediente=query($sql_expediente,$conexion);
        $fetch_expediente=fetch_array($resultado_expediente,$conexion); 

        $periodo_vacacion = $fetch_expediente[periodo_vacacion];

}

//if($cedula!='')
//{
//
//        $sql_persona = "SELECT  useruid
//                FROM   nompersonal 
//                WHERE  cedula='{$cedula}'";
//
//        $resultado_persona=query($sql_persona,$conexion);
//        $fetch_persona=fetch_array($resultado_persona,$conexion); 
//        $user_uid=$fetch_persona[useruid];        
//}

if($tipo==1)
{
    $sql_periodo = "SELECT  *
                FROM   periodos_vacaciones 
                WHERE  cedula='{$cedula}' AND saldo>=0 AND saldo <=29
                ORDER BY fini_periodo ASC";
}
else
{
    $sql_periodo = "SELECT  *
                FROM   periodos_vacaciones 
                WHERE  cedula='{$cedula}' AND saldo>=1 AND saldo <=30
                ORDER BY fini_periodo ASC";
}  

$resultado_periodo=query($sql_periodo,$conexion);

echo ' 
        <SELECT name="periodo_vacacion" class="form-control" id="periodo_vacacion" onchange="buscar_periodo_vacacion(this.value);">
            <option value="">Seleccione</option>';    
            while($fila=fetch_array($resultado_periodo,$conexion))
            {
                //echo "AQUI";
                if ($editar!=1)
                {                        

                        echo '<option  value="'.$fila['id'].'">DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';


                }
                else
                {
                    if($periodo_vacacion==$fila['id'])
                    {
                        echo '<option  value="'.$fila['id'].'" selected >DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';
                    }
                    else
                    {
                         echo '<option  value="'.$fila['id'].'">DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';
                    }         
                }

            }    
echo    '</SELECT>';

?>

