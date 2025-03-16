<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';
require_once '../../paginas/func_bd.php';

$conexion = conexion();

$cedula = $_GET['cedula'];
$editar = $_GET['editar'];
$codigo = $_GET['codigo'];
$subtipo = $_GET['subtipo'];


if($editar==1)
{


        $sql_expediente = "SELECT  *
                FROM   expediente 
                WHERE  cod_expediente_det='{$codigo}'";
        //echo $sql_expediente;

        $resultado_expediente=query($sql_expediente,$conexion);
        $fetch_expediente=fetch_array($resultado_expediente,$conexion); 

        $periodo_vacacion = $fetch_expediente['periodo_vacacion'];
        $resuelto = $fetch_expediente['resuelto'];

}

if($subtipo==110 || $subtipo==111)
{
    $sql_periodo = "SELECT  *
                FROM   periodos_vacaciones 
                WHERE  cedula='{$cedula}' AND saldo =30
                ORDER BY fini_periodo ASC";
    $resultado_periodo=query($sql_periodo,$conexion);
}

if($subtipo==114)
{
    $sql_periodo = "SELECT  *
                FROM   periodos_vacaciones 
                WHERE  cedula='{$cedula}' AND saldo>=0 AND saldo <=29 AND resueltas=1
                ORDER BY fini_periodo ASC";
    $resultado_periodo=query($sql_periodo,$conexion);
}

if($subtipo==115)
{
    $sql_periodo = "SELECT  *
                FROM   periodos_vacaciones 
                WHERE  cedula='{$cedula}' AND saldo>=1 AND saldo <=30 AND resueltas=1
                ORDER BY fini_periodo ASC";
    $resultado_periodo=query($sql_periodo,$conexion);            
}  





if($subtipo==110 || $subtipo==111 || $subtipo==114 || $subtipo==115)
{
    echo ' 
            <div class="form-group">
                <label class="col-md-2 control-label" for="txtcodigo">Periodo:</label>
                <div class="col-md-7">  
                    <SELECT name="periodo_vacacion" class="form-control" id="periodo_vacacion" onchange="buscar_periodo_vacacion(this.value);reset_campos();">
                        <option value="">Seleccione</option>';    
                        while($fila=fetch_array($resultado_periodo,$conexion))
                        {
                            //echo "AQUI";
                            if ($editar!=1)
                            {                        

                                    echo '<option  value="'.$fila['id_periodo_vacacion'].'">DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';


                            }
                            else
                            {
                                if($periodo_vacacion==$fila['id_periodo_vacacion'])
                                {
                                    echo '<option  value="'.$fila['id_periodo_vacacion'].'" selected >DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';
                                }
                                else
                                {
                                     echo '<option  value="'.$fila['id_periodo_vacacion'].'">DEL '.$fila['fini_periodo'].' AL '.$fila['ffin_periodo'].' DIAS RESTANTES: '.$fila['saldo'].'</option>';
                                }         
                            }

                        }    
        echo        '</SELECT>
                </div>
             </div>';
}

if($subtipo==110 || $subtipo==111 || $subtipo==114 || $subtipo==115)
{
    echo ' 
            <div class="form-group">
                <label class="col-md-2 control-label" for="txtcodigo">Fecha Ini. Periodo:</label>                                
                <div class="col-md-2">
                    <div class="input-group" data-date-format="dd/mm/yyyy" >
                        <input type="text" readonly name="fecha_inicio_periodo" class="form-control" id="fecha_inicio_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente['fecha_inicio_periodo']).'" maxlength="10"/>                                        
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>

                </div>

                <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo:</label>                                
                <div class="col-md-2">
                    <div class="input-group" data-date-format="dd/mm/yyyy" >
                        <input type="text" readonly name="fecha_fin_periodo" class="form-control" id="fecha_fin_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente['fecha_fin_periodo']).'" maxlength="10"/>                                                                                
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>

                </div>

            </div>
        ';
}
else
{
    echo ' 
            <div class="form-group">
                <label class="col-md-2 control-label" for="txtcodigo">Fecha Ini. Periodo:</label>                                
                <div class="col-md-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input type="text" name="fecha_inicio_periodo" readonly class="form-control" id="fecha_inicio_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente["fecha_inicio_periodo"]).'" maxlength="10"/>                                        
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>

                </div>

                <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo:</label>                                
                <div class="col-md-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input type="text" name="fecha_fin_periodo" readonly class="form-control" id="fecha_fin_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente["fecha_fin_periodo"]).'" maxlength="10"/>                                                                                
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>
            </div>           
            
        ';
}
if($subtipo==110 || $subtipo==111 || $subtipo==114 || $subtipo==115)
{
    echo'
            <div class="form-group">                             
                 <label class="col-md-2 control-label" for="txtcodigo">Resuelto:</label>                               
                <div class="col-md-2">
                    <input type="text" class="form-control"  name="numero_resolucion" id="numero_resolucion" maxlength="50" value="'.$fetch_expediente[numero_resolucion].'"/>                                
                </div>
                <label class="col-md-2 control-label" for="txtcodigo" >Fecha: </label>                               
                <div class="col-md-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input size="10" type="text" placeholder="(dd/mm/aaaa)" class="form-control" name="fecha_resolucion" id="fecha_resolucion" readonly value="'.fecha($fetch_expediente['fecha_resolucion']).'">                                                                  
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>       
            </div>

             <div class="form-group">
                <label class="col-md-2 control-label" for="txtcodigo">Fecha Salida:</label>                                
                <div class="col-md-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input type="text" name="fecha_inicio" class="form-control" id="fecha_inicio" placeholder="(dd/mm/aaaa)" size="10" onchange="calcular_dias();" value="'.fecha($fetch_expediente['fecha_inicio']).'" maxlength="10"/>                                        
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>

                </div>

                <label class="col-md-2 control-label" for="txtcodigo">Fecha Retorno:</label>                                
                <div class="col-md-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input type="text" name="fecha_fin" class="form-control" id="fecha_fin" placeholder="(dd/mm/aaaa)" size="10" onchange="calcular_dias();" value="'.fecha($fetch_expediente['fecha_fin']).'" maxlength="10"/>                                                                                
                        <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>

                </div>

            </div>
        ';
}

if($subtipo!=113)
{
    echo '      
            <div class="form-group">                             
                 <label class="col-md-2 control-label" for="txtcodigo">Días:</label>                               
                <div class="col-md-2">
                    <input type="text" class="form-control"  name="dias" id="dias" readonly maxlength="2" value="'.$fetch_expediente[dias].'"/>                                
                </div>
                <label class="col-md-2 control-label" for="txtcodigo">Saldo:</label>                               
                <div class="col-md-2">
                    <input type="text" class="form-control"  name="restante" id="restante" readonly maxlength="2" value="'.$fetch_expediente[restante].'"/>                                
                </div>
            </div>
        ';
}
else
{
    echo '      
            <div class="form-group">                             
                 <label class="col-md-2 control-label" for="txtcodigo">Días:</label>                               
                <div class="col-md-2">
                    <input type="text" class="form-control"  name="dias" id="dias" maxlength="2" value="'.$fetch_expediente["dias"].'"/>                                
                </div>
                <label class="col-md-2 control-label" for="txtcodigo">Saldo:</label>                               
                <div class="col-md-2">
                    <input type="text" class="form-control"  name="restante" id="restante" maxlength="2" value="'.$fetch_expediente["restante"].'"/>                                
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-md-2 control-label">Resuelto: </label>
                <div class="col-md-7">';
                   
                        $si = 'checked';													
                        $no = '';
                        if ($editar==1)
                        {
                            
                            if($resuelto==1)
                            {
                                $si = 'checked';													
                                $no = '';
                            }
                            else
                            {
                                $si = '';													
                                $no = 'checked';
                            }
                        }   

                    echo '
                    <div class="radio-list">
                        <label class="radio-inline">
                            <input type="radio" name="resuelto" id="si" value="1" '; echo $si; echo'> Si</label>
                        <label class="radio-inline">
                        <input type="radio" name="resuelto" id="no" value="0" '; echo $no; echo'> No</label>
                    </div>
                </div>
            </div>
        ';
}
?>

