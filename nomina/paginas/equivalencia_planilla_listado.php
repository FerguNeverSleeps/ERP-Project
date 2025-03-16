<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);


if($_REQUEST["accion"]=="ajax"):
    $start=$_REQUEST["iDisplayStart"];
    $limit=$_REQUEST["iDisplayLength"];

    $buscar=$_REQUEST["sSearch_0"];
    $buscar=json_decode($buscar,true);

    $add="";
    if(isset($buscar["horas_trabajadas_valor"]) and $buscar["horas_trabajadas_valor"]!="")
      $add.=" horas_trabajadas_valor ='".$buscar["horas_trabajadas_valor"]."' and";

    if(isset($buscar["horas_trabajadas_horas"]) and $buscar["horas_trabajadas_horas"]!="")
      $add.=" horas_trabajadas_horas ='".$buscar["horas_trabajadas_horas"]."' and";

    if(isset($buscar["horas_trabajadas_total"]) and $buscar["horas_trabajadas_total"]!="")
      $add.=" horas_trabajadas_total ='".$buscar["horas_trabajadas_total"]."' and";

    if(isset($buscar["feriados_i_cant"]) and $buscar["feriados_i_cant"]!="")
      $add.=" feriados_i_cant ='".$buscar["feriados_i_cant"]."' and";

    if($add)
      $add=" WHERE ".trim($add,"and");

    $orderby="";
    if($sort){
      foreach ($sort as $key => $value) 
        $orderby.="$key $value,";
      $orderby=" ORDER BY ".trim($orderby,","); 
    } 

    $limit_start="";
    if(!($start===NULL and $limit===NULL))
      $limit_start="LIMIT $limit OFFSET $start";

    $sql="select * from equivalencia_planilla $add";
    //print $sql;
    $return=[];
    $return["aaData"]=[];
    $row_index=$start+1;
    $index=0;
    $res = $db->query("$sql $orderby $limit_start");
    while($row = $res->fetch_assoc()){
      $row["row_index"]=$row_index++;
      $row["opt_editar"] = "<a onclick='onEditar(".$index.")' title='Editar'><img src='../../includes/imagenes/icons/pencil.png' width='16' height='16'></a>";
      $return["aaData"][]=$row;
      $index++;
    }
    
    $res = $db->query("select count(*) total from ($sql) t");
    $row = $res->fetch_assoc();
    $return["iTotalRecords"]= $return["iTotalDisplayRecords"]=$row["total"];
    $return["sEcho"]=intval($_REQUEST['sEcho']);
    print json_encode($return);
  exit;
elseif($_REQUEST["accion"]=="guardar"):
    $id_equivalencia_planilla=$_REQUEST["id_equivalencia_planilla"];
    $horas_trabajadas_valor=$_REQUEST["horas_trabajadas_valor"];
    $horas_trabajadas_horas=$_REQUEST["horas_trabajadas_horas"];
    $horas_trabajadas_total=$_REQUEST["horas_trabajadas_total"];
    $salario_basico_i_cant=$_REQUEST["salario_basico_i_cant"];
    $salario_basico_i_total=$_REQUEST["salario_basico_i_total"];
    $recargo_domingo_i_cant=$_REQUEST["recargo_domingo_i_cant"];
    $recargo_domingo_i_valor=$_REQUEST["recargo_domingo_i_valor"];
    $recargo_domingo_i_total=$_REQUEST["recargo_domingo_i_total"];
    $hora_extra_diurna_i_cant=$_REQUEST["hora_extra_diurna_i_cant"];
    $hora_extra_diurna_i_valor=$_REQUEST["hora_extra_diurna_i_valor"];
    $hora_extra_diurna_i_total=$_REQUEST["hora_extra_diurna_i_total"];
    $hora_extra_mixta_i_cant=$_REQUEST["hora_extra_mixta_i_cant"];
    $hora_extra_mixta_i_valor=$_REQUEST["hora_extra_mixta_i_valor"];
    $hora_extra_mixta_i_total=$_REQUEST["hora_extra_mixta_i_total"];
    $hora_extra_nocturna_i_cant=$_REQUEST["hora_extra_nocturna_i_cant"];
    $hora_extra_nocturna_i_valor=$_REQUEST["hora_extra_nocturna_i_valor"];
    $hora_extra_nocturna_i_total=$_REQUEST["hora_extra_nocturna_i_total"];
    $feriados_i_cant=$_REQUEST["feriados_i_cant"];
    $feriados_i_valor=$_REQUEST["feriados_i_valor"];
    $feriados_i_total=$_REQUEST["feriados_i_total"];
    $subtotal_asignaciones=$_REQUEST["subtotal_asignaciones"];
    $diferencia_120_iii_cant=$_REQUEST["diferencia_120_iii_cant"];
    $diferencia_120_iii_total=$_REQUEST["diferencia_120_iii_total"];
    $recargo_hora_extra_iii_cant=$_REQUEST["recargo_hora_extra_iii_cant"];
    $recargo_hora_extra_iii_valor=$_REQUEST["recargo_hora_extra_iii_valor"];
    $recargo_hora_extra_iii_total=$_REQUEST["recargo_hora_extra_iii_total"];
    $recargo_feriado_iii_cant=$_REQUEST["recargo_feriado_iii_cant"];
    $recargo_feriado_iii_valor=$_REQUEST["recargo_feriado_iii_valor"];
    $recargo_feriado_iii_total=$_REQUEST["recargo_feriado_iii_total"];
    $recargo_domingo_iii_cant=$_REQUEST["recargo_domingo_iii_cant"];
    $recargo_domingo_iii_valor=$_REQUEST["recargo_domingo_iii_valor"];
    $recargo_domingo_iii_total=$_REQUEST["recargo_domingo_iii_total"];
    $neto_pago=$_REQUEST["neto_pago"];


    if(!$id_equivalencia_planilla){
      $sql="INSERT INTO equivalencia_planilla(
          horas_trabajadas_valor,
          horas_trabajadas_horas,
          horas_trabajadas_total,
          salario_basico_i_cant,
          salario_basico_i_total,
          recargo_domingo_i_cant,
          recargo_domingo_i_valor,
          recargo_domingo_i_total,
          hora_extra_diurna_i_cant,
          hora_extra_diurna_i_valor,
          hora_extra_diurna_i_total,
          hora_extra_mixta_i_cant,
          hora_extra_mixta_i_valor,
          hora_extra_mixta_i_total,
          hora_extra_nocturna_i_cant,
          hora_extra_nocturna_i_valor,
          hora_extra_nocturna_i_total,
          feriados_i_cant,
          feriados_i_valor,
          feriados_i_total,
          subtotal_asignaciones,
          diferencia_120_iii_cant,
          diferencia_120_iii_total,
          recargo_hora_extra_iii_cant,
          recargo_hora_extra_iii_valor,
          recargo_hora_extra_iii_total,
          recargo_feriado_iii_cant,
          recargo_feriado_iii_valor,
          recargo_feriado_iii_total,
          recargo_domingo_iii_cant,
          recargo_domingo_iii_valor,
          recargo_domingo_iii_total,
          neto_pago)
        VALUES(
          '$horas_trabajadas_valor',
          '$horas_trabajadas_horas',
          '$horas_trabajadas_total',
          '$salario_basico_i_cant',
          '$salario_basico_i_total',
          '$recargo_domingo_i_cant',
          '$recargo_domingo_i_valor',
          '$recargo_domingo_i_total',
          '$hora_extra_diurna_i_cant',
          '$hora_extra_diurna_i_valor',
          '$hora_extra_diurna_i_total',
          '$hora_extra_mixta_i_cant',
          '$hora_extra_mixta_i_valor',
          '$hora_extra_mixta_i_total',
          '$hora_extra_nocturna_i_cant',
          '$hora_extra_nocturna_i_valor',
          '$hora_extra_nocturna_i_total',
          '$feriados_i_cant',
          '$feriados_i_valor',
          '$feriados_i_total',
          '$subtotal_asignaciones',
          '$diferencia_120_iii_cant',
          '$diferencia_120_iii_total',
          '$recargo_hora_extra_iii_cant',
          '$recargo_hora_extra_iii_valor',
          '$recargo_hora_extra_iii_total',
          '$recargo_feriado_iii_cant',
          '$recargo_feriado_iii_valor',
          '$recargo_feriado_iii_total',
          '$recargo_domingo_iii_cant',
          '$recargo_domingo_iii_valor',
          '$recargo_domingo_iii_total',
          '$neto_pago'
        )";
      }
    else{
      $sql="UPDATE equivalencia_planilla SET
          horas_trabajadas_valor='$horas_trabajadas_valor',
          horas_trabajadas_horas='$horas_trabajadas_horas',
          horas_trabajadas_total='$horas_trabajadas_total',
          salario_basico_i_cant='$salario_basico_i_cant',
          salario_basico_i_total='$salario_basico_i_total',
          recargo_domingo_i_cant='$recargo_domingo_i_cant',
          recargo_domingo_i_valor='$recargo_domingo_i_valor',
          recargo_domingo_i_total='$recargo_domingo_i_total',
          hora_extra_diurna_i_cant='$hora_extra_diurna_i_cant',
          hora_extra_diurna_i_valor='$hora_extra_diurna_i_valor',
          hora_extra_diurna_i_total='$hora_extra_diurna_i_total',
          hora_extra_mixta_i_cant='$hora_extra_mixta_i_cant',
          hora_extra_mixta_i_valor='$hora_extra_mixta_i_valor',
          hora_extra_mixta_i_total='$hora_extra_mixta_i_total',
          hora_extra_nocturna_i_cant='$hora_extra_nocturna_i_cant',
          hora_extra_nocturna_i_valor='$hora_extra_nocturna_i_valor',
          hora_extra_nocturna_i_total='$hora_extra_nocturna_i_total',
          feriados_i_cant='$feriados_i_cant',
          feriados_i_valor='$feriados_i_valor',
          feriados_i_total='$feriados_i_total',
          subtotal_asignaciones='$subtotal_asignaciones',
          diferencia_120_iii_cant='$diferencia_120_iii_cant',
          diferencia_120_iii_total='$diferencia_120_iii_total',
          recargo_hora_extra_iii_cant='$recargo_hora_extra_iii_cant',
          recargo_hora_extra_iii_valor='$recargo_hora_extra_iii_valor',
          recargo_hora_extra_iii_total='$recargo_hora_extra_iii_total',
          recargo_feriado_iii_cant='$recargo_feriado_iii_cant',
          recargo_feriado_iii_valor='$recargo_feriado_iii_valor',
          recargo_feriado_iii_total='$recargo_feriado_iii_total',
          recargo_domingo_iii_cant='$recargo_domingo_iii_cant',
          recargo_domingo_iii_valor='$recargo_domingo_iii_valor',
          recargo_domingo_iii_total='$recargo_domingo_iii_total',
          neto_pago='$neto_pago'
        WHERE 
          id='$id_equivalencia_planilla'
      ";
    }
    $res = $db->query($sql);

    print json_encode(["success"=>true]);
    exit;
endif;


?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">

.portlet > .portlet-title > .actions > .btn {
    padding: 4px 10px !important;
    margin-top: -14px !important;
}
#table_datatable tbody tr td{
  vertical-align: middle;
}

#table_datatable_length .form-control {
    padding: 0px;
}

div.dataTables_filter label {
    float: left;
}

#table_datatable_wrapper .dataTables_filter input {
   margin-top: 5px;
   margin-bottom: 5px;
}

#table_datatable_wrapper div.dataTables_length label {
  margin-top: 5px;
}

@media (min-width: 513px) {
  #search_situ.input-small {
      width: 122px !important;
  }
}

#table_datatable thead {
  color: white; 
  background: #00BCD4;
}

#table_datatable thead th {
  text-align: center;
}

.fieldset {
  display: flex;
  margin-top: 10px;
  border-bottom: 1px solid #BDBDBD;
}

.legend {
  font-size: 20px;
  flex: 0.4;
  padding-top: 25px;
  padding-left: 10px;
  color: #1f1f1f;
}

.fieldset .row {
  flex: 0.6;
}

tr.filter td{
  padding: 0 !important;
}

.form-control {
  border-color: #999999 !important;
}

#btnBuscar,
#btnLimpiar {
  color: #03A9F4;
  font-size: 16px;
  margin: 7px 3px;
  cursor: pointer;
}

#btnBuscar {
  margin-left: 7px;
}

.dataTables_filter{
  display: none;
}

.input-sm {
  padding: 5px 5px !important;
}

</style>
<div class="page-container">
  <!-- BEGIN SIDEBAR -->
  <!-- END SIDEBAR -->
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                Equivalencias Planilla
              </div>
              <div class="actions"> 
                <a class="btn btn-sm blue"  onclick="onAgregar()">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>        
              </div>
            </div>
            <div class="portlet-body">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
                <thead>
                  <tr role="row">
                    <th rowspan="2">#</th>
                    <th colspan="3">Horas Trabajadas</th>
                    <th colspan="2">Salario Básico</th>
                    <th colspan="3">Recargo Domingo</th>
                    <th colspan="3">Hora Extra<br>Diurna</th>
                    <th colspan="3">Hora Extra<br>Mixta</th>
                    <th colspan="3">Hora Extra<br>Nocturna</th>
                    <th colspan="3">Feriados</th>
                    <th rowspan="2">Sub Total<br>Asig.</th>
                    <th colspan="2">Diferencia<br>de 120H</th>
                    <th colspan="3">Recargo<br>Horas Extra</th>
                    <th colspan="3">Recargo<br>Feriado</th>
                    <th colspan="3">Recargo<br>Domingo</th>
                    <th rowspan="2">Neto a<br>pagar</th>     
                    <th rowspan="2"></th>           
                  </tr>
                  <tr role="row">                                          
                    <th>Valor</th>
                    <th>Horas</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>                      
                    <th>Valor</th>
                    <th>Total</th>                    
                    <th>Cant</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                    <th>Cant</th>
                    <th>Valor</th>
                    <th>Total</th>
                  </tr>
                  <tr role="row" class="filter" style="background: white;">
                    <td></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_trabajadas_valor"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_trabajadas_horas"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_trabajadas_total"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_feriados_i_cant"></dh>
                    <td>
                      <i id="btnBuscar" class="fa fa-search filter-submit"></i>
                      <i id="btnLimpiar" class="fa fa-close filter-submit"></i>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>                    
                    <td colspan="16"></td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
  <div class="modal fade" id="modal-editor">
    <div class="modal-dialog" style="width: 800px;">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Ingreso de Equivalencia</h4>
        </div>
        <div class="modal-body" style="zoom: 0.9;">
          <input type="hidden" id="id_equivalencia_planilla" value="">

          <div class="fieldset">
            <div class="legend">Horas Trabajadas</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="horas_trabajadas_valor">
              </div>
              <div class="col-sm-4">
                <label>Horas</label>
                <input class="form-control" type="text" value="" id="horas_trabajadas_horas">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="horas_trabajadas_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Salario Básico</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4"></div>
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="salario_basico_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="salario_basico_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Recargo Domingo</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_i_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Hora Extra Diurna</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="hora_extra_diurna_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="hora_extra_diurna_i_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="hora_extra_diurna_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Hora Extra Mixta</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="hora_extra_mixta_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="hora_extra_mixta_i_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="hora_extra_mixta_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Hora Extra Nocturna</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="hora_extra_nocturna_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="hora_extra_nocturna_i_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="hora_extra_nocturna_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Feriados</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="feriados_i_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="feriados_i_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="feriados_i_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Sub Total Asig.</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <label>SubTotal</label>
                <input class="form-control" type="text" value="" id="subtotal_asignaciones">
              </div>
            </div>
          </div>



          <div class="fieldset">
            <div class="legend">Diferencia de 120H</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4"></div>
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="diferencia_120_iii_cant">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="diferencia_120_iii_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Recargo Horas Extra</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="recargo_hora_extra_iii_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="recargo_hora_extra_iii_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="recargo_hora_extra_iii_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Recargo Feriado</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="recargo_feriado_iii_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="recargo_feriado_iii_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="recargo_feriado_iii_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Recargo Domingo</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
                <label>Cantidad</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_iii_cant">
              </div>
              <div class="col-sm-4">
                <label>Valor</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_iii_valor">
              </div>
              <div class="col-sm-4">
                <label>Total</label>
                <input class="form-control" type="text" value="" id="recargo_domingo_iii_total">
              </div>
            </div>
          </div>

          <div class="fieldset">
            <div class="legend">Neto a Pagar</div>
            <div class="form-group m-form__group row">
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
              </div>
              <div class="col-sm-4">
                <label>Neto</label>
                <input class="form-control" type="text" value="" id="neto_pago">
              </div>
            </div>
          </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-success" data-dismiss="modal" onclick="onGuardar()">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">

  function onAgregar(){
    $("#modal-editor").modal("show");
    $("#id_equivalencia_planilla").val("");
    $("#horas_trabajadas_valor").val("");
    $("#horas_trabajadas_horas").val("");
    $("#horas_trabajadas_total").val("");
    $("#salario_basico_i_cant").val("");
    $("#salario_basico_i_total").val("");
    $("#recargo_domingo_i_cant").val("");
    $("#recargo_domingo_i_valor").val("");
    $("#recargo_domingo_i_total").val("");
    $("#hora_extra_diurna_i_cant").val("");
    $("#hora_extra_diurna_i_valor").val("");
    $("#hora_extra_diurna_i_total").val("");
    $("#hora_extra_mixta_i_cant").val("");
    $("#hora_extra_mixta_i_valor").val("");
    $("#hora_extra_mixta_i_total").val("");
    $("#hora_extra_nocturna_i_cant").val("");
    $("#hora_extra_nocturna_i_valor").val("");
    $("#hora_extra_nocturna_i_total").val("");
    $("#feriados_i_cant").val("");
    $("#feriados_i_valor").val("");
    $("#feriados_i_total").val("");
    $("#subtotal_asignaciones").val("");
    $("#diferencia_120_iii_cant").val("");
    $("#diferencia_120_iii_total").val("");
    $("#recargo_hora_extra_iii_cant").val("");
    $("#recargo_hora_extra_iii_valor").val("");
    $("#recargo_hora_extra_iii_total").val("");
    $("#recargo_feriado_iii_cant").val("");
    $("#recargo_feriado_iii_valor").val("");
    $("#recargo_feriado_iii_total").val("");
    $("#recargo_domingo_iii_cant").val("");
    $("#recargo_domingo_iii_valor").val("");
    $("#recargo_domingo_iii_total").val("");
    $("#neto_pago").val("");
  }

  function onEditar(index){
    var data=$('#table_datatable').DataTable().fnSettings().aoData[index]["_aData"];
    console.log(data);
    $("#modal-editor").modal("show");
    $("#id_equivalencia_planilla").val(data["id"]);
    $("#horas_trabajadas_valor").val(data["horas_trabajadas_valor"]);
    $("#horas_trabajadas_horas").val(data["horas_trabajadas_horas"]);
    $("#horas_trabajadas_total").val(data["horas_trabajadas_total"]);
    $("#salario_basico_i_cant").val(data["salario_basico_i_cant"]);
    $("#salario_basico_i_total").val(data["salario_basico_i_total"]);
    $("#recargo_domingo_i_cant").val(data["recargo_domingo_i_cant"]);
    $("#recargo_domingo_i_valor").val(data["recargo_domingo_i_valor"]);
    $("#recargo_domingo_i_total").val(data["recargo_domingo_i_total"]);
    $("#hora_extra_diurna_i_cant").val(data["hora_extra_diurna_i_cant"]);
    $("#hora_extra_diurna_i_valor").val(data["hora_extra_diurna_i_valor"]);
    $("#hora_extra_diurna_i_total").val(data["hora_extra_diurna_i_total"]);
    $("#hora_extra_mixta_i_cant").val(data["hora_extra_mixta_i_cant"]);
    $("#hora_extra_mixta_i_valor").val(data["hora_extra_mixta_i_valor"]);
    $("#hora_extra_mixta_i_total").val(data["hora_extra_mixta_i_total"]);
    $("#hora_extra_nocturna_i_cant").val(data["hora_extra_nocturna_i_cant"]);
    $("#hora_extra_nocturna_i_valor").val(data["hora_extra_nocturna_i_valor"]);
    $("#hora_extra_nocturna_i_total").val(data["hora_extra_nocturna_i_total"]);
    $("#feriados_i_cant").val(data["feriados_i_cant"]);
    $("#feriados_i_valor").val(data["feriados_i_valor"]);
    $("#feriados_i_total").val(data["feriados_i_total"]);
    $("#subtotal_asignaciones").val(data["subtotal_asignaciones"]);
    $("#diferencia_120_iii_cant").val(data["diferencia_120_iii_cant"]);
    $("#diferencia_120_iii_total").val(data["diferencia_120_iii_total"]);
    $("#recargo_hora_extra_iii_cant").val(data["recargo_hora_extra_iii_cant"]);
    $("#recargo_hora_extra_iii_valor").val(data["recargo_hora_extra_iii_valor"]);
    $("#recargo_hora_extra_iii_total").val(data["recargo_hora_extra_iii_total"]);
    $("#recargo_feriado_iii_cant").val(data["recargo_feriado_iii_cant"]);
    $("#recargo_feriado_iii_valor").val(data["recargo_feriado_iii_valor"]);
    $("#recargo_feriado_iii_total").val(data["recargo_feriado_iii_total"]);
    $("#recargo_domingo_iii_cant").val(data["recargo_domingo_iii_cant"]);
    $("#recargo_domingo_iii_valor").val(data["recargo_domingo_iii_valor"]);
    $("#recargo_domingo_iii_total").val(data["recargo_domingo_iii_total"]);
    $("#neto_pago").val(data["neto_pago"]);



  }

  function onGuardar(){
    var id_equivalencia_planilla=$("#id_equivalencia_planilla").val();
    var horas_trabajadas_valor=$("#horas_trabajadas_valor").val();
    var horas_trabajadas_horas=$("#horas_trabajadas_horas").val();
    var horas_trabajadas_total=$("#horas_trabajadas_total").val();
    var salario_basico_i_cant=$("#salario_basico_i_cant").val();
    var salario_basico_i_total=$("#salario_basico_i_total").val();
    var recargo_domingo_i_cant=$("#recargo_domingo_i_cant").val();
    var recargo_domingo_i_valor=$("#recargo_domingo_i_valor").val();
    var recargo_domingo_i_total=$("#recargo_domingo_i_total").val();
    var hora_extra_diurna_i_cant=$("#hora_extra_diurna_i_cant").val();
    var hora_extra_diurna_i_valor=$("#hora_extra_diurna_i_valor").val();
    var hora_extra_diurna_i_total=$("#hora_extra_diurna_i_total").val();
    var hora_extra_mixta_i_cant=$("#hora_extra_mixta_i_cant").val();
    var hora_extra_mixta_i_valor=$("#hora_extra_mixta_i_valor").val();
    var hora_extra_mixta_i_total=$("#hora_extra_mixta_i_total").val();
    var hora_extra_nocturna_i_cant=$("#hora_extra_nocturna_i_cant").val();
    var hora_extra_nocturna_i_valor=$("#hora_extra_nocturna_i_valor").val();
    var hora_extra_nocturna_i_total=$("#hora_extra_nocturna_i_total").val();
    var feriados_i_cant=$("#feriados_i_cant").val();
    var feriados_i_valor=$("#feriados_i_valor").val();
    var feriados_i_total=$("#feriados_i_total").val();
    var subtotal_asignaciones=$("#subtotal_asignaciones").val();
    var diferencia_120_iii_cant=$("#diferencia_120_iii_cant").val();
    var diferencia_120_iii_total=$("#diferencia_120_iii_total").val();
    var recargo_hora_extra_iii_cant=$("#recargo_hora_extra_iii_cant").val();
    var recargo_hora_extra_iii_valor=$("#recargo_hora_extra_iii_valor").val();
    var recargo_hora_extra_iii_total=$("#recargo_hora_extra_iii_total").val();
    var recargo_feriado_iii_cant=$("#recargo_feriado_iii_cant").val();
    var recargo_feriado_iii_valor=$("#recargo_feriado_iii_valor").val();
    var recargo_feriado_iii_total=$("#recargo_feriado_iii_total").val();
    var recargo_domingo_iii_cant=$("#recargo_domingo_iii_cant").val();
    var recargo_domingo_iii_valor=$("#recargo_domingo_iii_valor").val();
    var recargo_domingo_iii_total=$("#recargo_domingo_iii_total").val();
    var neto_pago=$("#neto_pago").val();


    $.ajax({
        url  : 'equivalencia_planilla_listado.php?accion=guardar',
        type : 'POST',
        data : {
          id_equivalencia_planilla: id_equivalencia_planilla,
          horas_trabajadas_valor: horas_trabajadas_valor,
          horas_trabajadas_horas: horas_trabajadas_horas,
          horas_trabajadas_total: horas_trabajadas_total,
          salario_basico_i_cant: salario_basico_i_cant,
          salario_basico_i_total: salario_basico_i_total,
          recargo_domingo_i_cant: recargo_domingo_i_cant,
          recargo_domingo_i_valor: recargo_domingo_i_valor,
          recargo_domingo_i_total: recargo_domingo_i_total,
          hora_extra_diurna_i_cant: hora_extra_diurna_i_cant,
          hora_extra_diurna_i_valor: hora_extra_diurna_i_valor,
          hora_extra_diurna_i_total: hora_extra_diurna_i_total,
          hora_extra_mixta_i_cant: hora_extra_mixta_i_cant,
          hora_extra_mixta_i_valor: hora_extra_mixta_i_valor,
          hora_extra_mixta_i_total: hora_extra_mixta_i_total,
          hora_extra_nocturna_i_cant: hora_extra_nocturna_i_cant,
          hora_extra_nocturna_i_valor: hora_extra_nocturna_i_valor,
          hora_extra_nocturna_i_total: hora_extra_nocturna_i_total,
          feriados_i_cant: feriados_i_cant,
          feriados_i_valor: feriados_i_valor,
          feriados_i_total: feriados_i_total,
          subtotal_asignaciones: subtotal_asignaciones,
          diferencia_120_iii_cant: diferencia_120_iii_cant,
          diferencia_120_iii_total: diferencia_120_iii_total,
          recargo_hora_extra_iii_cant: recargo_hora_extra_iii_cant,
          recargo_hora_extra_iii_valor: recargo_hora_extra_iii_valor,
          recargo_hora_extra_iii_total: recargo_hora_extra_iii_total,
          recargo_feriado_iii_cant: recargo_feriado_iii_cant,
          recargo_feriado_iii_valor: recargo_feriado_iii_valor,
          recargo_feriado_iii_total: recargo_feriado_iii_total,
          recargo_domingo_iii_cant: recargo_domingo_iii_cant,
          recargo_domingo_iii_valor: recargo_domingo_iii_valor,
          recargo_domingo_iii_total: recargo_domingo_iii_total,
          neto_pago: neto_pago
        },
        success : function(response) {
            $('#table_datatable').DataTable()._fnDraw();
            $("#modal-editor").modal('hide');
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
      });
  }


$(document).ready(function() { 
  $('.fancybox').fancybox( {topRatio:0,width:1000} );

  var oTable = $('#table_datatable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
                "bStateSave" : true,
                "sAjaxSource": "equivalencia_planilla_listado.php?accion=ajax", 
                "sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "iDisplayLength": 10,
              "sPaginationType": "bootstrap_extended",
              "aaSorting": [[ 1, "asc" ]],
                "oLanguage": {
                  "sProcessing": "Cargando...",
                  "sSearch": "",
                    "sLengthMenu": "Mostrar _MENU_",
                    "sInfoEmpty": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles", 
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",
                        "sNext": "P&aacute;gina Siguiente",
                        "sPage": "P&aacute;gina",
                        "sPageOf": "de",
                    }
                },
                "aLengthMenu": [ 
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],  
                "aoColumns": [ 
                  {"mData": "row_index"},
                  {"mData": "horas_trabajadas_valor"}, 
                  {"mData": "horas_trabajadas_horas"}, 
                  {"mData": "horas_trabajadas_total"}, 
                  {"mData": "salario_basico_i_cant"}, 
                  {"mData": "salario_basico_i_total"}, 
                  {"mData": "recargo_domingo_i_cant"}, 
                  {"mData": "recargo_domingo_i_valor"}, 
                  {"mData": "recargo_domingo_i_total"}, 
                  {"mData": "hora_extra_diurna_i_cant"}, 
                  {"mData": "hora_extra_diurna_i_valor"}, 
                  {"mData": "hora_extra_diurna_i_total"}, 
                  {"mData": "hora_extra_mixta_i_cant"}, 
                  {"mData": "hora_extra_mixta_i_valor"}, 
                  {"mData": "hora_extra_mixta_i_total"}, 
                  {"mData": "hora_extra_nocturna_i_cant"}, 
                  {"mData": "hora_extra_nocturna_i_valor"}, 
                  {"mData": "hora_extra_nocturna_i_total"}, 
                  {"mData": "feriados_i_cant"}, 
                  {"mData": "feriados_i_valor"}, 
                  {"mData": "feriados_i_total"}, 
                  {"mData": "subtotal_asignaciones"}, 
                  {"mData": "diferencia_120_iii_cant"}, 
                  {"mData": "diferencia_120_iii_total"}, 
                  {"mData": "recargo_hora_extra_iii_cant"}, 
                  {"mData": "recargo_hora_extra_iii_valor"}, 
                  {"mData": "recargo_hora_extra_iii_total"}, 
                  {"mData": "recargo_feriado_iii_cant"}, 
                  {"mData": "recargo_feriado_iii_valor"}, 
                  {"mData": "recargo_feriado_iii_total"}, 
                  {"mData": "recargo_domingo_iii_cant"}, 
                  {"mData": "recargo_domingo_iii_valor"}, 
                  {"mData": "recargo_domingo_iii_total"}, 
                  {"mData": "neto_pago"},
                  {"mData": "opt_editar"}
                ],  
                "aoColumnDefs": [
                  //{ 'bSortable':   false, 'aTargets': [0,34] }
                  { 'bSortable':   false, 'aTargets': [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34] }
                ],  
                "fnDrawCallback": function() {
                  $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                  createEvents();
                },
                initComplete: function(){
                  createEvents();
                }
            });

        /*oTable.on('select', function (e, dt, type, indexes) {
                   //var bla = dt.row({selected: true}).data().yourField
                   console.log("select");
            });*/

        $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
        $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-small"); 
        $('#table_datatable_wrapper .dataTables_length select').select2({
            showSearchInput : false //hide search box with special css class
        }); // initialize select2 dropdown

        //$('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");

        //$('#table_datatable_wrapper .dataTables_filter input').after(' <a class="btn blue" id="btn-search"><i class="fa fa-search"></i> Buscar</a> ');

            
        $("#btn-search").click( function()
        {
           var valor_buscar =$('#search_situ').val();

           if( valor_buscar == 'Todos' )
           {
            valor_buscar = '';
           }

           // Se filtra por la columna 4 - Situación
           oTable.fnFilter( valor_buscar, 4 );
        });

        $("#btnBuscar").click(function(){
          var horas_trabajadas_valor=$("#buscar_horas_trabajadas_valor").val();
          var horas_trabajadas_horas=$("#buscar_horas_trabajadas_horas").val();
          var horas_trabajadas_total=$("#buscar_horas_trabajadas_total").val();
          var feriados_i_cant=$("#buscar_feriados_i_cant").val();

          var buscar={
            horas_trabajadas_valor: horas_trabajadas_valor,
            horas_trabajadas_horas: horas_trabajadas_horas,
            horas_trabajadas_total: horas_trabajadas_total,
            feriados_i_cant: feriados_i_cant
          };

          oTable.fnPageChange("first",false);
          oTable.fnFilter( JSON.stringify(buscar), 0 , true);
        }); 

        $("#btnLimpiar").click(function(){
          $("#buscar_horas_trabajadas_valor").val("");
          $("#buscar_horas_trabajadas_horas").val("");
          $("#buscar_horas_trabajadas_total").val("");
          $("#buscar_feriados_i_cant").val("");

          oTable.fnPageChange("first",false);
          oTable.fnFilter( JSON.stringify([]), 0 , true);
        });

        function createEvents() {
          $('.modalreporte').off().on('click', function(){

            var url = $(this).attr('rel');
            $('#reportepdf').attr('href', url);
            var url = $(this).attr('rel2');
            $('#reportepdf2').attr('href', url);
            var url = $(this).attr('name');
            $('#reporteexcel').attr('href', url);
              $("#modal-reporte").modal('show');

          });
        }

});
</script>
<!-- manuel -->
<script type="text/javascript" src="../../reporte_pub/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../reporte_pub/css/jquery.fancybox.css" media="screen" />
</body>
</html>