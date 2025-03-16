<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	/*$sql            = "SELECT nomposicion_id
						FROM nompersonal b 
						WHERE nomposicion_id='".$nomposicion_id."'";´*/
$sql = "SELECT b.cargo_id,b.nomposicion_id, b.sueldo_propuesto,b.sueldo_1,b.sueldo_2,b.sueldo_3,b.sueldo_4,
    b.mes_1,b.mes_2,b.mes_3,b.mes_4,b.partida011,b.partida012,b.partida013,b.partida019,b.partida080,
    b.sobresueldo_antiguedad_1,b.sobresueldo_zona_apartada_1,b.sobresueldo_jefatura_1,b.sobresueldo_otros_1,
    b.mes_ant_1,b.mes_za_1,b.mes_jef_1,b.mes_ot_1,b.partida030,b.partida, b.unidad,b.gastos_representacion,
    b.mes_gasto_1, b.sobresueldo_antiguedad_2,b.sobresueldo_zona_apartada_2,b.sobresueldo_jefatura_2,b.sobresueldo_otros_2,
    b.sobresueldo_esp_1,b.mes_esp_1,b.sobresueldo_esp_2,b.mes_esp_2,b.sueldo_propuesto_11,b.sueldo_propuesto_12,
    b.sueldo_propuesto_13,b.sueldo_propuesto_19,b.sueldo_propuesto_30,b.sueldo_propuesto_80,b.gasto_representacion_2,
    b.mes_ant_2,b.mes_za_2,b.mes_jef_2,b.mes_ot_2,b.mes_gasto_2, b.estado
        FROM nomposicion b
        WHERE  b.nomposicion_id='".$nomposicion_id."'";

	$res            = $db->query($sql); 

    $fila = $res->fetch_assoc();

    if(count( $fila )>=1){


    
	//echo $fila['nomposicion_id'];
$sql2   = "SELECT COUNT(b.nomposicion_id) as total
            FROM nompersonal b
            WHERE  b.nomposicion_id='".$fila['nomposicion_id']."' AND (b.estado LIKE '%Activo%' OR b.estado LIKE '%REGULAR%' OR b.estado LIKE '%INTERINO%' OR b.estado LIKE '%RESERVADO%' OR b.estado LIKE '%Vacaciones%' OR b.estado LIKE  '%Licencias con Sueldo%')";
//echo $sql2,"<br>";
            $res2  = $db->query($sql2);
	    $fila2 = $res2->fetch_assoc();
            
            
            $sql3 = "SELECT * FROM nomcargos WHERE  cod_car='".$fila['cargo_id']."'";
            //echo $sql2,"<br>";
            $res3 = $db->query($sql3);
	    $fila3 = $res3->fetch_assoc();
            $gremio =0;
            $bandera=1;
            $unidad='';
//echo $fila2["total"],"<br>";
            if($fila3["gremio"]==0)
                $gremio ='Administrativo';
            else
                $gremio ='Gremio';
        $partida = $fila["partida"];
        if(!$fila["partida"] || $fila["partida"]=='0' || $fila["partida"]=='' || $fila["partida"]==null){$fila["partida"]='Sin Partida'; $partida='0';}
        if(!$fila["partida011"] || $fila["partida011"]=='0' || $fila["partida011"]=='' || $fila["partida011"]==null )$fila["partida011"]='Sin Partida';
        if(!$fila["partida012"] || $fila["partida012"]=='0' || $fila["partida012"]=='' || $fila["partida012"]==null)$fila["partida012"]='Sin Partida';
        if(!$fila["partida013"] || $fila["partida013"]=='0' || $fila["partida013"]=='' || $fila["partida013"]==null)$fila["partida013"]='Sin Partida';
        if(!$fila["partida019"] || $fila["partida019"]=='0' || $fila["partida019"]=='' || $fila["partida019"]==null)$fila["partida019"]='Sin Partida';
        if(!$fila["partida080"] || $fila["partida080"]=='0' || $fila["partida080"]=='' || $fila["partida080"]==null)$fila["partida080"]='Sin Partida';
        if(!$fila["partida030"] || $fila["partida030"]=='0' || $fila["partida030"]=='' || $fila["partida030"]==null)$fila["partida030"]='Sin Partida';
        if(!$fila["unidad"] || $fila["unidad"]=='0' || $fila["unidad"]=='' || $fila["unidad"]==null){$fila["unidad"]='Sin Unidad'; $bandera=0;}
        if($bandera==1)
        {
            $unidad=$fila["unidad"];
            $sql_unidad = "SELECT * FROM nomnivel2 WHERE  codorg='".$unidad."'";
            //echo $sql2,"<br>";
            $res_unidad = $db->query($sql_unidad);
	    $fila_unidad = $res_unidad->fetch_assoc();
            $descripcion_unidad = $fila_unidad["descrip"];
            
        } 
        $sueldo_anual = $fila["sueldo_1"]*$fila["mes_1"] + $fila["sueldo_2"]*$fila["mes_2"] + 
                $fila["sueldo_3"]*$fila["mes_3"] + $fila["sueldo_4"]*$fila["mes_4"];
        $meses_total = $fila["mes_1"] + $fila["mes_2"] + $fila["mes_3"] + $fila["mes_4"];
        
	if($fila2["total"]==0)
	{          
            
            echo '  
            <div class="control-label form-group">
                    <div class="col-md-3">&nbsp;</div>
                    <div class="col-md-6">
                    <input type="hidden" id="hid_posicion_disponible" name="hid_posicion_disponible" value="1">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="label label-success">Posición Disponible</label>
                    </div>
            </div>';            
	}
	else
	{
            echo '

            <div class="control-label form-group">
                <div class="col-md-3">&nbsp;
                </div>
                <div class="col-md-6">
                    <input type="hidden" id="hid_posicion_disponible" name="hid_posicion_disponible" value="0">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="label label-danger">Posición No Disponible</label>
                </div>
            </div>';
        }
            echo '
            </br>
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"> 
                          <a  id="btn-toggle">Desglose de Sueldos</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="collapse">
                        <div class="panel-body">

                            <div class="control-label form-group">
                                <div class="col-md-3"><strong>Categoria Funcionario</strong>
                                </div>
                                <div class="col-md-8">
                                    '.$gremio.'
                                </div>                                
                            </div>
                            </br>
                            <div class="control-label form-group">
                                <div class="col-md-3"><strong>Unidad</strong>
                                </div>
                                <div class="col-md-8">
                                    <input type="hidden" id="f_unidad" name="f_unidad" value="'.$unidad.'">'.$fila["unidad"].' '.$descripcion_unidad.'
                                </div>                            
                            </div>
                            </br>

                            <div class="form-group">                                
                                <label class="col-md-4 control-label" for="txtcodigo"><strong>Partidas: </strong></label> 
                                <label class="col-md-4 control-label" for="txtcodigo"></label>                            
                            </div>

                            <div class="form-group" style="position: relative; left: 5%;" >
                                <table class="table table-striped table-hover" style="width: 90%">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th>Principal&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>Sobresueldo&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>Gastos Representación&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <tr>                                       
                                            <td>
                                                Partida
                                            </td>
                                            <td>                                           
                                                '.$fila["partida"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["partida080"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["partida030"].'
                                            </td>                          
                                        </tr>

                                    </tbody>
                                </table>
                            </div>                            
                            
                            <div class="form-group">                                
                                <label class="col-md-4 control-label" for="txtcodigo"><strong>Partidas (cont.): </strong></label> 
                                <label class="col-md-4 control-label" for="txtcodigo"></label>                            
                            </div>

                            <div class="form-group" style="position: relative; left: 5%;" >
                                <table class="table table-striped table-hover" style="width: 90%">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th>Dieta&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>Combustiblr&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <tr>                                       
                                            <td>
                                                Partida
                                            </td>
                                            <td>                                           
                                                '.$fila["partida_dieta"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["partida_combustible"].'                                            
                                            </td>               
                                        </tr>

                                    </tbody>
                                </table>
                            </div>               

                            <div class="form-group">                                
                                <label class="col-md-4 control-label" for="txtcodigo"><strong>Sueldos Presupuestados (001): </strong></label> 
                                <label class="col-md-4 control-label" for="txtcodigo"></label>                            
                            </div>

                            <div class="form-group" style="position: relative; left: 5%;" >
                                <table class="table table-striped table-hover" style="width: 90%">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th>1&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>2&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>3&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>4&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>Anual&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        <tr>                                       
                                            <td>
                                                Sueldo
                                            </td>
                                            <td>                                           
                                                '.$fila["sueldo_1"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["sueldo_2"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sueldo_3"].'
                                            </td>
                                            <td>
                                                '.$fila["sueldo_4"].'                                            
                                            </td>
                                            <td>
                                                '.$sueldo_anual.'                                            
                                            </td>    
                                        </tr>
                                        <tr>                                       
                                            <td>
                                                Meses
                                            </td>
                                            <td>
                                                '.$fila["mes_1"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_2"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_3"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_4"].'
                                            </td>
                                            <td>
                                                '.$meses_total.'                                            
                                            </td>    
                                        </tr>      
                                    </tbody>
                                </table>
                            </div>

                            <div class="form-group">                                
                                <label class="col-md-4 control-label" for="txtcodigo"><strong>Sobresueldos (010): </strong></label> 
                                <label class="col-md-4 control-label" for="txtcodigo"></label>                            
                            </div>

                            <div class="form-group" style="position: relative; left: 5%;" >
                                 <table class="table table-striped table-hover" style="width: 90%">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th>SUELDO (1)&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>MESES (1)&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>SUELDO (2)&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>MESES (2)&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>SUELDO PROPUESTO &nbsp;&nbsp;&nbsp;&nbsp;</th>
                                      </tr>
                                    </thead>
                                    <tbody>                                        
                                         <tr>                                       
                                            <td>
                                                SOBRESUELDO (080)
                                            </td>
                                            <td>                                           
                                                '.$fila["sobresueldo_otros_1"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["mes_ot_1"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sobresueldo_otros_2"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_ot_2"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sueldo_propuesto_80"].'                                            
                                            </td>  
                                        </tr>
                                         <tr>                                       
                                            <td>
                                                GASTOS REPRESENTACION (030)
                                            </td>
                                            <td>                                           
                                                '.$fila["gastos_representacion"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["mes_gasto_1"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["gasto_representacion_2"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_gasto_2"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sueldo_propuesto_30"].'                                            
                                            </td>  
                                        </tr>
                                        <tr>                                       
                                            <td>
                                                DIETA
                                            </td>
                                            <td>                                           
                                                '.$fila["sobresueldo_dieta_1"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["mes_dieta_1"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sobresueldo_dieta_2"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_dieta_2"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sueldo_propuesto_dieta"].'                                            
                                            </td>  
                                        </tr>
                                        <tr>                                       
                                            <td>
                                                COMBUSTIBLE
                                            </td>
                                            <td>                                           
                                                '.$fila["sobresueldo_combustible_1"].'                                            
                                            </td>
                                            <td>                                           
                                                '.$fila["mes_combustible_1"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sobresueldo_combustible_2"].'
                                            </td>
                                            <td>
                                                '.$fila["mes_combustible_2"].'                                            
                                            </td>
                                            <td>
                                                '.$fila["sueldo_propuesto_combustible"].'                                            
                                            </td>  
                                        </tr>
                                    </tbody>
                                </table>                               
                                
                            </div>                       
                        </div>                    
                    </div>
                </div>
            </div>
            <input id="hid_partida" type="hidden" value="'.$partida.'">';
	
    }
    else{
        echo '
            <div class="row">
                <div class="control-label form-group">
                    <div class="col-md-3">&nbsp;
                    </div>
                    <div class="col-md-6">
                        <input type="hidden" id="hid_posicion_disponible" name="hid_posicion_disponible" value="0">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="label label-default">Posición inexistente</label>
                    </div>
                </div>
            </div> <br>';
    }
?>