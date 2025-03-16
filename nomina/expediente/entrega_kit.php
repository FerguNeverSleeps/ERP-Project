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
            $conexion = conexion();
            
            $sql = "SELECT a.*,b.codorg,b.descrip 
            FROM   nompersonal a 
            LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
            WHERE  cedula='{$_GET['cedula']}'";

            $res1=query($sql,$conexion);

            $empleado = $res1->fetch_object();

            if(isset($_GET['codigo']) && $_GET['codigo']!='')
            {

                $sql = "SELECT * 
                                FROM   expediente
                                WHERE  cod_expediente_det='{$_GET['codigo']}'";

                $res=query($sql,$conexion);

                $expediente = $res->fetch_object();

                $fecha = DateTime::createFromFormat('Y-m-d', $expediente->fecha);
                $fecha = ($fecha !== false) ? $fecha->format('d/m/Y') : '';	
            }
        ?>
        <div class="form-horizontal margin-top-30">

            <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">ASUNTO:</label>
                <div class="col-sm-7">
                    <input type="text" name="descripcion" class="form-control" id="descripcion" readonly="true" 
                    <? if (isset($expediente->descripcion)) { echo "value='$expediente->descripcion'"; }else{ echo "value='Entrega de Kit de Nuevo Ingreso'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">PARA:</label>
                <div class="col-sm-7">
                    <input type="text" name="descripcion" class="form-control" id="descripcion" readonly="true" 
                    <? if (isset($empleado->apenom)) { echo "value='$empleado->apenom'"; }else{ echo "value='Entrega de Kit de Nuevo Ingreso'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <label for="proyecto" class="col-sm-2 control-label">SUCURSAL:</label>
                <div class="col-sm-7">
                    <input type="text" name="proyecto" class="form-control" id="proyecto" readonly="true" 
                    <? if (isset($empleado->descrip)) { echo "value='$empleado->descrip'"; }else{ echo "value='No asignado'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">Fecha:</label>
                <div class="col-sm-2">
                    <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                        <input type="text" class="form-control" name="fecha" id="fecha" placeholder="dd/mm/aaaa" value="<?php echo isset($fecha) ? $fecha : date('d/m/Y'); ?>" >
                        <span class="input-group-btn">
                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8"><h4>Suéter  MMD</h4><hr></div>
                    <div class="col-sm-6">
                        <label for="seccion_anterior" class="col-sm-2 control-label">CANTIDAD:</label>
                        <div class="col-sm-6">
                            <input type="number" name="seccion_anterior" class="form-control" id="seccion_anterior" 
                            <? if (isset($expediente->seccion_anterior)) { echo "value='$expediente->seccion_anterior'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="tipo_estudio" class="col-sm-2 control-label">TALLA:</label>
                        <div class="col-sm-6">
                            <input type="text" name="tipo_estudio" class="form-control" id="tipo_estudio" 
                            <? if (isset($expediente->tipo_estudio)) { echo "value='$expediente->tipo_estudio'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8"><h4>Llave Kaba</h4><hr></div>
                    <div class="col-sm-6">
                        <label for="seccion_nueva" class="col-sm-2 control-label">CANTIDAD:</label>
                        <div class="col-sm-6">
                            <input type="number" name="seccion_nueva" class="form-control" id="seccion_nueva" 
                            <? if (isset($expediente->seccion_nueva)) { echo "value='$expediente->seccion_nueva'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="nivel_actual" class="col-sm-2 control-label">TALLA:</label>
                        <div class="col-sm-6">
                            <input type="text" name="nivel_actual" class="form-control" id="nivel_actual" 
                            <? if (isset($expediente->nivel_actual)) { echo "value='$expediente->nivel_actual'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8"><h4>Carné</h4><hr></div>
                    <div class="col-sm-6">
                        <label for="funcion_anterior" class="col-sm-2 control-label">CANTIDAD:</label>
                        <div class="col-sm-6">
                            <input type="number" name="funcion_anterior" class="form-control" id="funcion_anterior" 
                            <? if (isset($expediente->funcion_anterior)) { echo "value='$expediente->funcion_anterior'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="cargo_estructura" class="col-sm-2 control-label">TALLA:</label>
                        <div class="col-sm-6">
                            <input type="text" name="cargo_estructura" class="form-control" id="cargo_estructura" 
                            <? if (isset($expediente->cargo_estructura)) { echo "value='$expediente->cargo_estructura'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8"><h4>Reglamento Interno</h4><hr></div>
                    <div class="col-sm-6">
                        <label for="funcion_nueva" class="col-sm-2 control-label">CANTIDAD:</label>
                        <div class="col-sm-6">
                            <input type="number" name="funcion_nueva" class="form-control" id="funcion_nueva" 
                            <? if (isset($expediente->funcion_nueva)) { echo "value='$expediente->funcion_nueva'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="cargo_funcion" class="col-sm-2 control-label">TALLA:</label>
                        <div class="col-sm-6">
                            <input type="text" name="cargo_funcion" class="form-control" id="cargo_funcion" 
                            <? if (isset($expediente->cargo_funcion)) { echo "value='$expediente->cargo_funcion'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-8"><h4>Manual de Identidad empresarial y arreglo personal</h4><hr></div>
                    <div class="col-sm-6">
                        <label for="funcion_nueva" class="col-sm-2 control-label">CANTIDAD:</label>
                        <div class="col-sm-6">
                            <input type="number" name="funcion_nueva" class="form-control" id="funcion_nueva" 
                            <? if (isset($expediente->funcion_nueva)) { echo "value='$expediente->funcion_nueva'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="concepto" class="col-sm-2 control-label">TALLA:</label>
                        <div class="col-sm-6">
                            <input type="text" name="concepto" class="form-control" id="concepto" 
                            <? if (isset($expediente->concepto)) { echo "value='$expediente->concepto'"; }else{ echo "value=''"; }?> />
                            
                        </div>
                    </div>
                    
                    <hr>
                </div>
            </div>
            <br>
        </div>
</div>
<?php
