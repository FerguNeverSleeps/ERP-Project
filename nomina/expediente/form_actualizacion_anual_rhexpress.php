<div id="registro">

        <style>
                .row{
                        margin-top: 15px;
                }
                .margin-top-30{
                        margin-top: 30px; 
                }
        </style>
        <?php
        //echo $_GET['codigo'];exit;
                if(isset($_GET['codigo']) && $_GET['codigo']!='')
                {
                        $conexion = conexion();

                        // $sql = "SELECT nombre_documento, descripcion, url_documento, fecha_registro, fecha_vencimiento 
                        //                 FROM   expediente_documento 
                        //                 WHERE  cod_expediente_det='{$_GET['codigo']}'";
                        $sql="SELECT * FROM expediente WHERE cod_expediente_det='{$_GET['codigo']}'";                     

                        $res=query($sql,$conexion);

                        $documento = $res->fetch_object();

                        $fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $documento->fecha_vencimiento);
                        $fecha_vencimiento = ($fecha_vencimiento !== false) ? $fecha_vencimiento->format('d/m/Y') : '';	
                }
        ?>
        <!--<div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:  </label>                                
            <div class="col-md-7">
                <select name="tipo_tiporegistro" id="tipo_tiporegistro" class="form-control">
                    <?php                                
                        while($fila=fetch_array($resultado)){?>                                    			
                    <option value="<?=$fila['id_expediente_subtipo']?>"><?=  utf8_encode($fila['nombre_subtipo'])?></option>;
                        <?} ?>

                </select>
           </div>
        </div>-->
        <div class="form-horizontal margin-top-30">
            <!-- <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">Tipo de reclamo:</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="tipo_reclamo" id="tipo_reclamo" value="<? echo $documento->numero_resolucion_anterior; ?>">
                </div>
          </div> -->
          <!-- <div class="row">
            <label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n:</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" name="tipo_reclamo" id="tipo_reclamo" value="<? echo $documento->descripcion; ?>">                
            </div>
          </div> -->
          <!-- <div class="row">
            <label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n del reclamo:</label>
            <div class="col-sm-7">
            <input type="text" class="form-control" name="tipo_reclamo" id="tipo_reclamo" value="<? echo $documento->concepto; ?>">                
            </div>
          </div> -->
          <!-- <div class="row">
            <label for="descripcion" class="col-sm-2 control-label">Monto:</label>
            <div class="col-sm-7">
            <input type="text" class="form-control" name="tipo_reclamo" id="tipo_reclamo" value="<? echo $documento->monto; ?>">               
            </div>
          </div> -->
          <div class="row">
            <label for="descripcion" class="col-sm-2 control-label">Fecha de solicitud:</label>
            <div class="col-sm-2">
                <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                    <input type="text" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" placeholder="dd/mm/aaaa" value="<?php echo $documento->fecha;?>" >
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
            </div>
          </div>
          <?php
          if($_GET['codigo']=='')
          {
                ?>
                          <div class="row">
                                <label for="archivo" class="col-sm-2 control-label">Cargar documento:</label>
                            <div class="col-sm-5">
                                <input type="file" name="archivo" id="archivo" accept="image/*" style="border: none; outline:none;">
                            </div>
                          </div>
                <?php
          }
          else
          { ?> <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <embed src="<?php echo $documento->url_documento; ?>" width="550" height="300" >
                    </div>
                </div>
            <?php
          }
          ?>
          </div>
</div>
<?php
