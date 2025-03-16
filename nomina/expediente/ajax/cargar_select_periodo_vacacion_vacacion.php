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
    $sql_periodo = "SELECT periodo
                    FROM nom_progvacaciones 
                    WHERE ceduda = '{$cedula}'
                    ORDER BY periodo DESC";
    $resultado_periodo=query($sql_periodo,$conexion);
}




if($subtipo==110 || $subtipo==111 || $subtipo==112)
{
    echo ' 
            <div class="form-group">
                    <div class="col-md-4"></div>
                    <div class="col-md-6">
                            <label> Disfrute
                            <input type="radio" value="1" name="tipo" id="tipo1" />
                            <span></span>
                        </label>
                        <label> Disfrute y Pagados
                            <input type="radio" value="2" name="tipo" id="tipo2" checked />
                            <span></span>
                        </label>
                        <label> Pagados
                            <input type="radio" value="3" name="tipo" id="tipo3" />
                            <span></span>
                        </label>
                         <label> Vac. Acumuladas
                            <input type="radio" value="4" name="tipo" id="tipo4" />
                            <span></span>
                        </label>
                    </div>
            </div>
        ';
}


if($subtipo==110 || $subtipo==111 || $subtipo==112)
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

                                    echo '<option  value="'.$fila['periodo'].'">'.$fila['periodo'].'</option>';


                            }
                            else
                            {
                                if($periodo_vacacion==$fila['periodo'])
                                {
                                    echo '<option  value="'.$fila['periodo'].'" selected >'.$fila['periodo'].'/option>';
                                }
                                else
                                {
                                     echo '<option  value="'.$fila['periodo'].'">'.$fila['periodo'].' </option>';
                                }         
                            }

                        }    
        echo        '</SELECT>
                </div>
             </div>';
}

//if($subtipo==110 || $subtipo==111 || $subtipo==112)
//{
//    echo ' 
//            <div class="form-group">
//                <label class="col-md-2 control-label" for="txtcodigo">Fecha Ini. Periodo:</label>                                
//                <div class="col-md-2">
//                    <div class="input-group" data-date-format="dd/mm/yyyy" >
//                        <input type="text" readonly name="fecha_inicio_periodo" class="form-control" id="fecha_inicio_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente['fecha_inicio_periodo']).'" maxlength="10"/>                                        
//                        <span class="input-group-btn">
//                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
//                        </span>
//                    </div>
//
//                </div>
//
//                <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo:</label>                                
//                <div class="col-md-2">
//                    <div class="input-group" data-date-format="dd/mm/yyyy" >
//                        <input type="text" readonly name="fecha_fin_periodo" class="form-control" id="fecha_fin_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente['fecha_fin_periodo']).'" maxlength="10"/>                                                                                
//                        <span class="input-group-btn">
//                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
//                        </span>
//                    </div>
//
//                </div>
//
//            </div>
//        ';
//}
//else
//{
//    echo ' 
//            <div class="form-group">
//                <label class="col-md-2 control-label" for="txtcodigo">Fecha Ini. Periodo:</label>                                
//                <div class="col-md-2">
//                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
//                        <input type="text" name="fecha_inicio_periodo" readonly class="form-control" id="fecha_inicio_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente["fecha_inicio_periodo"]).'" maxlength="10"/>                                        
//                        <span class="input-group-btn">
//                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
//                        </span>
//                    </div>
//
//                </div>
//
//                <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo:</label>                                
//                <div class="col-md-2">
//                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
//                        <input type="text" name="fecha_fin_periodo" readonly class="form-control" id="fecha_fin_periodo" placeholder="(dd/mm/aaaa)" size="10" value="'.fecha($fetch_expediente["fecha_fin_periodo"]).'" maxlength="10"/>                                                                                
//                        <span class="input-group-btn">
//                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
//                        </span>
//                    </div>
//                </div>
//            </div>           
//            
//        ';
//}

if($subtipo==110)
{
    echo'
            <div id="fechas">
                <div class="form-group margin-top-15">

                        <label class="control-label col-md-2">Fecha Salida</label>
                        <div class="col-md-2">
                                <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                                    <input type="text" name="fecha_inicio" class="form-control" id="fecha_inicio" placeholder="(dd/mm/aaaa)" size="10" onchange="calcular_dias();" value="'.fecha($fetch_expediente['fecha_inicio']).'" maxlength="10"/>                                        
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                        </div>
               

                        <label class="control-label col-md-2">Fecha Retorno</label>
                        <div class="col-md-2">
                                <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                                    <input type="text" name="fecha_fin" class="form-control" id="fecha_fin" placeholder="(dd/mm/aaaa)" size="10" onchange="calcular_dias();" value="'.fecha($fetch_expediente['fecha_fin']).'" maxlength="10"/>                                                                                
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                        </div>
                </div>
            </div>
            <div id="pagados">

                    <div class="form-group">

                            <label class="control-label col-md-2">Días Vacaciones</label>
                            <div class="col-md-2">
                                    <input type="text" name="diasvac" id="diasvac" class="form-control"  value="" readonly="readonly">
                            </div>

                            <label class="control-label col-md-2">Saldo Vacaciones</label>
                            <div class="col-md-2">
                                    <input type="text" name="saldo_vacaciones" id="saldo_vacaciones" class="form-control" value="" readonly="readonly">
                            </div>
                    </div>	
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Solicitados/Pagar</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_solic_ppagar" id="dias_solic_ppagar" class="form-control" value="'.$fetch_expediente[dias_solic_ppagar].'"  >
                            </div>
                    </div>
            </div>											
            <div id = "disfrute">
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Vacaciones Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_vac_disfrute" id="dias_vac_disfrute" class="form-control"  value=""  readonly="readonly">
                            </div>

                            <label class="control-label col-md-2">Saldo Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="saldo_dias_pdisfrutar" id="saldo_dias_pdisfrutar" class="form-control" value="" readonly="readonly" >
                            </div>
                    </div>	
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Solicitados Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_solic_pdisfrutar" id="dias_solic_pdisfrutar" class="form-control" value="'.$fetch_expediente[dias_solic_pdisfrutar].'" >
                            </div>
                    </div>	


            </div>
        ';
}

if($subtipo==111 || $subtipo==112)
{
    echo'
            
            <div id="pagados">

                    <div class="form-group">

                            <label class="control-label col-md-2">Días Vacaciones</label>
                            <div class="col-md-2">
                                    <input type="text" name="diasvac" id="diasvac" class="form-control"  value="" readonly="readonly">
                            </div>

                            <label class="control-label col-md-2">Saldo Vacaciones</label>
                            <div class="col-md-2">
                                    <input type="text" name="saldo_vacaciones" id="saldo_vacaciones" class="form-control" value="" readonly="readonly">
                            </div>
                    </div>	
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Solicitados/Pagar</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_solic_ppagar" id="dias_solic_ppagar" class="form-control" value="'.$fetch_expediente[dias_solic_ppagar].'"  >
                            </div>
                    </div>
            </div>											
            <div id = "disfrute">
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Vacaciones Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_vac_disfrute" id="dias_vac_disfrute" class="form-control"  value=""  readonly="readonly">
                            </div>

                            <label class="control-label col-md-2">Saldo Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="saldo_dias_pdisfrutar" id="saldo_dias_pdisfrutar" class="form-control" value="" readonly="readonly" >
                            </div>
                    </div>	
                    <div class="form-group">

                            <label class="control-label col-md-2">Días Solicitados Disfrute</label>
                            <div class="col-md-2">
                                    <input type="text" name="dias_solic_pdisfrutar" id="dias_solic_pdisfrutar" class="form-control" value="'.$fetch_expediente[dias_solic_pdisfrutar].'" placeholder="Días solicitados por disfrutar">
                            </div>
                    </div>	


            </div>
        ';
}
?>

