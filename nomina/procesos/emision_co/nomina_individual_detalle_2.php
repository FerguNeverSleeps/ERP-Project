<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?


//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';
require_once '../../paginas/func_bd.php';
include ("../../paginas/funciones_nomina.php");
//include ("../../header.php");

$conexion=conexion();

$id_cabecera = $_REQUEST['id_cabecera'];
$opcion    = $_REQUEST['opcion'];





$consulta_tabla= "SELECT a.*, b.*,c.* 
                    FROM emision_col_cabecera as a 
                    LEFT JOIN  emision_col_detalle_request as b ON (a.id_cabecera=b.id_cabecera) 
                    LEFT JOIN  emision_col_detalle_response as c ON (b.id_detalle_request=c.id_detalle_request) 
                    WHERE a.id_cabecera='$id_cabecera'
                    ORDER BY b.id_detalle_request ASC ";
$resultado_tabla=query($consulta_tabla,$conexion);

$consulta_id= "SELECT id_cabecera "
          . " FROM emision_col_cabecera as a "
          . " WHERE a.id_cabecera='$id_cabecera' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$id=$fila_id['id'];

include ("../../header_emision.php");
$Modelos = ["Token","Parametros"];
$Controladores = ["Token", "Parametros"];

foreach ($Modelos as $key => $value)
{    
    require_once "./modelo/{$value}.modelo.php";
}
foreach ($Controladores as $key => $value)
{   
    require_once "./controlador/{$value}.controlador.php";
}

$Parametros = new ParametrosControlador();
$Token = new TokenControlador();
//?>

<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
  <div class="page-content-wrapper">
    <div class="page-content">
      <div class="row">
        <div class="col-md-12">
           <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
                    <div class="caption">
                    <img src="../../imagenes/21.png" width="20" height="20" class="icon"> Nomina Emisión Electronica / Detalle
                    </div>
                    <div class="actions">

                            
                            <a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_individual.php'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                    </div>
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      <th>ID</th>  
                      <th>Consecutivo</th>
                      <th>Cedula</th>
                      <th>Periodo Nomina</th>
                      <th>Rango Numeracion</th>
                      <th>Tipo Documento</th>
                      <th>Total Comprobante</th>
                      <th>Total Deducciones</th>
                      <th>Total Devengados</th>
                      <th>Codigo</th>
                      <th>Mensaje</th>
                      <th>Resultado</th>
                      <th>CUNE</th>
                      <!--<th>QR</th>-->
                      <th>DIAN</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 12px; ">                    
                      <?php
                          $data_request=[];
                          $data_response=[];
                          function td_decode($str){
                            $tmp=json_decode(base64_decode($str));
                            return $tmp;
                          }
                          $i=0;
                          $actualizar_status = 0;
                          while ($fila_tabla=fetch_array($resultado_tabla,$conexion))
                          {
                            
                            $folder = "./pdf/";
                            $archivo = $folder.$fila_tabla['codigo'].".pdf";
                            $data_cune = "";
                            
                            $esValidoDian = $fila_tabla["esvalidoDIAN"];
                            $mensaje = $fila_tabla['mensaje'];
                            $resultado = $fila_tabla['resultado'];
                            $cune = $dfila_tablaata['cune'];
                            $reglasRechazoEmision = $fila_tabla['reglasRechazoEmision'];
                            $reglasRechazoDIAN =$fila_tabla['reglasRechazoDIAN'];
                            if($fila_tabla['esvalidoDIAN'] == 0){
                              $consulta = TokenControlador::consultarNominaIndividual( $fila_tabla['codigo'] );
                              $consulta = json_decode( $consulta, true );
                              $api_mensaje_result = $consulta["dianResponse"]["responseDian"]["Envelope"]["Body"]["GetStatusResponse"]["GetStatusResult"];
                              $data = $consulta["data"];

                              if($api_mensaje_result["IsValid"] == true){
                                $datos_nomina = array(
                                  "ficha"=> $fila_tabla['ficha'],
                                  "id_cabecera" => $id_cabecera,
                                  "data" => $data 
  
                                );
                                if(!file_exists( $archivo ))
                                {
                                  $pdf = $data["pdf_base64_bytes"];
                                  $base_pdf = base64_decode($pdf);
                                  file_put_contents($archivo, $base_pdf);
                                }
                                $data_cune =  $data['signed_xml_key'];
                                $data_actualizar = array(
                                  "codigo"               => $fila_tabla['codigo'],
                                  "id_detalle_request"   => $fila_tabla['id_detalle_request'],
                                  "cune"                 => $data['signed_xml_key'],
                                  "trackId"              => $data['consecutive_id'],
                                  "mensaje"              => $api_mensaje_result['StatusDescription'],
                                  "resultado"            => $api_mensaje_result['StatusMessage'],
                                  "reglasRechazoEmision" => $api_mensaje_result['ErrorMessage'][0],
                                  "reglasRechazoDIAN"    => json_encode($api_mensaje_result['ErrorMessage']),
                                  "qr"                   => $api_mensaje_result['XmlFileName'],
                                  "esvalidoDIAN"         => $api_mensaje_result["IsValid"] ? 1 : 0
                                );
  
                                Parametros::ActualizarNominaIndivual( $data_actualizar );
                                $actualizar_status = 1;
                                
                                
                                $esValidoDian         = $api_mensaje_result["IsValid"] ? 1 : 0;
                                $mensaje              = $api_mensaje_result['StatusMessage'];
                                $resultado            = $api_mensaje_result['StatusDescription'];
                                $data_cune            = $data['signed_xml_key'];
                                $reglasRechazoEmision = $api_mensaje_result['ErrorMessage'][0];
                                $reglasRechazoDIAN    = $api_mensaje_result['ErrorMessage'][0];

                              }else{
                                $data_cune =  $data['signed_xml_key'];
                                $data_actualizar = array(
                                  "id_detalle_request"   => $fila_tabla['id_detalle_request'],
                                  "cune"                 => $data['signed_xml_key'],
                                  "trackId"              => $data['consecutive_id'],
                                  "mensaje"              => $api_mensaje_result['StatusDescription'],
                                  "resultado"            => $api_mensaje_result['StatusMessage'],
                                  "reglasRechazoEmision" => json_encode($api_mensaje_result['ErrorMessage']),
                                  "reglasRechazoDIAN"    => json_encode($api_mensaje_result['ErrorMessage']),
                                  "qr"                   => $api_mensaje_result['XmlFileName'],
                                  "esvalidoDIAN"         => $api_mensaje_result["IsValid"] ? 1 : 0
                                );
  
                                $esValidoDian         = $api_mensaje_result["IsValid"] ? 1 : 0;
                                Parametros::ActualizarNominaIndivual( $data_actualizar );

                              }
                              $reglasRechazoDIAN = json_encode($api_mensaje_result['ErrorMessage']);
                            }
                            else{
                              $actualizar_status = 1;
                              
                            }
                            $cune = ($fila_tabla["cune"] != "") ? $fila_tabla["cune"]  : $data_cune;

                            $data_request[]=[
                              "objNomina"=>[
                                "consecutivoDocumentoNom"       => $fila_tabla['consecutivoDocumentoNom'],
                                "deducciones"                   => td_decode($fila_tabla['deducciones']),
                                "devengados"                    => td_decode($fila_tabla['devengados']),
                                "documentosReferenciadosNom"    => td_decode($fila_tabla['documentosReferenciadosNom']),
                                "extrasNom"                     => td_decode($fila_tabla['extrasNom']),
                                "fechaEmisionNom"               => td_decode($fila_tabla['fechaEmisionNom']),
                                "notas"                         => td_decode($fila_tabla['notas']),
                                "novedad"                       => td_decode($fila_tabla['novedad']),
                                "novedadCUNE"                   => td_decode($fila_tabla['novedadCUNE']),
                                "lugarGeneracionXML"            => td_decode($fila_tabla['lugarGeneracionXML']),
                                "pagos"                         => td_decode($fila_tabla['pagos']),
                                "periodoNomina"                 => td_decode($fila_tabla['periodoNomina']),
                                "periodos"                      => td_decode($fila_tabla['periodos']),
                                "rangoNumeracionNom"            => td_decode($fila_tabla['rangoNumeracionNom']),
                                "redondeo"                      => td_decode($fila_tabla['redondeo']),
                                "tipoMonedaNom"                 => td_decode($fila_tabla['tipoMonedaNom']),
                                "tipoNota"                      => td_decode($fila_tabla['tipoNota']),
                                "totalComprobante"              => td_decode($fila_tabla['totalComprobante']),
                                "totalDeducciones"              => td_decode($fila_tabla['totalDeducciones']),
                                "totalDevengados"               => td_decode($fila_tabla['totalDevengados']),
                                "trm"                           => td_decode($fila_tabla['trm']),
                                "trabajador"                    => td_decode($fila_tabla['trabajador'])
                              ]
                            ];

                            $data_response[]=[
                              "codigo"                          => $fila_tabla['codigo'],
                              "mensaje"                         => utf8_encode($mensaje),
                              "resultado"                       => $resultado,
                              "consecutivoDocumento"            => $fila_tabla['consecutivoDocumentoNom'],
                              "cune"                            => $cune,
                              "trackId"                         => $fila_tabla['trackId'],
                              "reglasNotificacionesEmision"       => td_decode($fila_tabla['reglasNotificacionesEmision']),
                              "reglasNotificacionesDIAN"        => td_decode($fila_tabla['reglasNotificacionesDIAN']),
                              "reglasRechazoEmision"              => td_decode($reglasRechazoEmision),
                              "reglasRechazoDIAN"               => ($reglasRechazoDIAN),
                              "nitEmpleador"                    => $fila_tabla['nitEmpleador'],
                              "nitEmpleado"                     => $fila_tabla['nitEmpleado'],
                              "idSoftware"                      => $fila_tabla['idSoftware'],
                              "qr"                              => td_decode($fila_tabla['qr']),
                              "esvalidoDIAN"                    => td_decode($fila_tabla['esvalidoDIAN']),
                              "xml"                             => td_decode($fila_tabla['xml'])
                            ];

                          $add_cls="background-color: #ffcdd2; color: #d32f2f;";
                          if($esValidoDian==1)
                            $add_cls="background-color: #c8e6c9; color: #004d40;";

                          ?>
                            <tr id="<?php echo $fila_tabla['id_detalle_request'];?>" style="<?php print $add_cls;?>">
                              <td><?php echo $fila_tabla['id_detalle_request']; ?></td>
                              <td><?php echo $fila_tabla['consecutivoDocumentoNom']; ?></td>
                              <td><?php echo $fila_tabla['cedula']; ?></td>
                              <td><?php echo ($fila_tabla['periodoNomina']); ?></td>
                              <td><?php echo ($fila_tabla['rangoNumeracionNom']); ?></td>
                              <td><?php echo ($fila_tabla['tipoDocumentoNom']); ?></td>
                              <td style="text-align: right;"><?php echo ($fila_tabla['totalComprobante']); ?></td>
                              <td style="text-align: right;"><?php echo ($fila_tabla['totalDevengados']); ?></td>
                              <td style="text-align: right;"><?php echo ($fila_tabla['totalDeducciones']); ?></td>
                              <td><?php echo $fila_tabla['codigo']; ?></td>
                              <td><?php echo utf8_encode($mensaje); ?></td>
                              <td><?php echo $resultado; ?></td>
                              <td><?php echo $cune; ?></td>
                              <!--<td><?php echo td_decode($fila_tabla['qr']); ?></td>-->
                              <td><?php echo td_decode($esValidoDian); ?></td>
                              <td>
                                <div align="center">
                                    <font size="2" face="Arial, Helvetica, sans-serif">
                                        <a onclick="ver(<?php echo($i); ?>);"><img src="../../../includes/imagenes/icons/eye.png" alt="Ver" width="16" height="16" border="0" align="absmiddle"></a>
                                        &nbsp;
                                        <?php 
                                        if($esValidoDian==1):
                                          ?>
                                        <a target="_blank" href="https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=<?php echo $cune; ?>" title="DIAN"><img src="../../../includes/imagenes/icons/favicon_dian.ico" width="16" height="16"></a>
                                        &nbsp;
                                        <a target="_blank" href="<?= $archivo ?>" title="PDF"><img src="../../../includes/imagenes/icons/bar-code-16.png" width="16" height="16"></a>
                                        <?php endif ?>
                                    </font>
                                </div>
                              </td>
                            </tr>
                            <?php
                            $i++;
                            }
                            ?>
                  </tbody>
                  
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
$consulta_id= "SELECT count(*) cant FROM emision_col_detalle_response as a  WHERE a.id_cabecera='$id_cabecera' AND a.esvalidoDIAN= '0';";
$resultado_id=query($consulta_id,$conexion);
$fila_cant=fetch_array($resultado_id,$conexion);

  if($fila_cant['cant'] == 0){
    ParametrosControlador::ActualizarEstatusCabecera($id_cabecera);
  }
  ?>
    <input name="txtficha" id="txtficha" type="hidden" value="<?php echo $ficha; ?>">    
    <input name="cedula" id="cedula" type="hidden" value="<?php echo $cedula; ?>">
    <input name="registro_id" id="registro_id" type="hidden" value="">  
    <input name="op" type="hidden" value="">  
  </form>
  <?php include("../../footer_emision.php"); ?>
<script type="text/javascript">
  var data_request=<?php print json_encode($data_request);?>;
  var data_response=<?php print json_encode($data_response);?>;

   $(document).ready(function() { 
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 25,
              "bStateSave" : true,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended",
              "aaSorting": [[ 0, "asc" ]],
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    /*"oPaginate": {
                        "sPrevious": "Página Anterior",
                        "sNext": "Página Siguiente"
                    }*/
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [14] },
                    { "bSearchable": false, "aTargets": [14 ] },
                    { "sWidth": "5%", "aTargets": [14] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
                /*
              "aoColumns": [
            null,
            null,
            { "sWidth": "10px" },
            { "sWidth": "10px" },
        ]*/
              /*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });



function ver(index){
  
  var obj_request=data_request[index];
  $("#data_request").html(prettyPrint(obj_request));

  var obj_response=data_response[index];
  $("#data_response").html(prettyPrint(obj_response));

  $("#modal_ver").modal("show");
}

function modal_aceptar(){
    window.location.href="?anio=<?php print $_REQUEST['anio']?>&mes=<?php print $_REQUEST['mes']?>&opcion=3&id_detalle="+$("#modal-id-detalle").val()+"&dias_enfermedad="+$("#modal-dias-enfermedad").val()+"&observaciones="+$("#modal-observaciones").val();
}

/*
Object.prototype.prettyPrint = function(){
    var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
    var replacer = function(match, pIndent, pKey, pVal, pEnd) {
        var key = '<span class="json-key" style="color: brown">',
            val = '<span class="json-value" style="color: navy">',
            str = '<span class="json-string" style="color: olive">',
            r = pIndent || '';
        if (pKey)
            r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
        if (pVal)
            r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
        return r + (pEnd || '');
    };

    return JSON.stringify(this, null, 3)
               .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
               .replace(/</g, '&lt;').replace(/>/g, '&gt;')
               .replace(jsonLine, replacer);
}

*/

var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
function replacer(match, pIndent, pKey, pVal, pEnd) {
    var key = '<span class="json-key" style="color: brown">',
        val = '<span class="json-value" style="color: navy">',
        str = '<span class="json-string" style="color: olive">',
        r = pIndent || '';
    if (pKey)
        r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
    if (pVal)
        r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
    return r + (pEnd || '');
};

function prettyPrint(obj){
  return JSON.stringify(obj, null, 3)
               .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
               .replace(/</g, '&lt;').replace(/>/g, '&gt;')
               .replace(jsonLine, replacer);
}


</script>
<style type="text/css">

  pre {
    background-color: ghostwhite;
    border: 1px solid silver;
    padding: 10px 20px;
    margin: 20px;
    border-radius: 4px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
   }

   pre code {
      padding: 0;
      font-size: inherit;
      color: inherit;
      white-space: pre-wrap;
      background-color: transparent;
      border: none;
      box-shadow: none;
      border-radius: 0;
  }

</style>


    <!--MODAL-->
    <div class="modal fade" id="modal_ver" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" style="width: 900px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Detalle</h4>
                </div>
                <div class="modal-body">

                  <ul class="nav nav-tabs">
                      <li class="active"><a href="#tab_request" id="tab-request" data-toggle="tab">Request</a></li>
                      <li><a href="#tab_response" id="tab-response" data-toggle="tab">Response</a></li>                                
                  </ul>

                  <div class="tab-content">  

                      <div class="tab-pane active" id="tab_request">
                        <pre><code id=data_request></code></pre>
                      </div>

                      <div class="tab-pane" id="tab_response">
                        <pre><code id=data_response></code></pre>
                      </div>

                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


</body>
</html>