<?php 
echo '
<div class="modal fade" id="filtro" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Imprimir listado de Cheques</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-right">Fecha Inicio</div>
                    <div class="col-md-6" id="messages" role="alert">
                    <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd-mm-yyyy"> 
                         <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="" maxlength="60">
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 text-right">Fecha Fin</div>
                    <div class="col-md-6" id="messages" role="alert">
                        <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd-mm-yyyy"> 
                         <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="" maxlength="60">
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 text-right">Formato</div>
                    <div class="col-md-6" id="messages" role="alert">
                        <select name="tipo_reporte" id="tipo_reporte"  class="form-control" maxlength="60">
                            <option value="_pdf" selected>PDF</option>
                            <option value="_excel">EXCEL</option>
                        </select>
                    </div>
                </div><br>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn blue" id="btnFiltro"><i class="fa fa-print"></i> &nbsp;Imprimir</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->';