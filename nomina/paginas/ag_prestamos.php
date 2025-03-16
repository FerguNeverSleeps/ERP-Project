<?php 
session_start();
ob_start();
	include ("../header4.php");
	include("../lib/common.php");
	include("func_bd.php");	

?>

<script>

function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 
		document.frmPrincipal.op_tp.value=1}
	else{ 
		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.txtdescripcion.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");}
}

</script>



<?php 
	
	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];
	
	if ($registro_id==0) 
  {// Si el registro_id es 0 se va a agregar un registro nuevo
				
		if ($op_tp==1)
    {		
      $conexion = conexion();
		
  		$codigo_nuevo=AgregarCodigo("nomprestamos","codigopr");
  		
  		$query="insert into nomprestamos 
  		(codigopr,codigo_proveedor,descrip,formula,markar,ee,subclave, id_categoria_acreedor,descrip_corto)
  		values ($codigo_nuevo,"
                        . "'$_POST[txtcodigoproveedor]',"
                        . "'$_POST[txtdescripcion]',"
                        . "'$_POST[txtconcepto]',"
                        . "0,"
                        . "'$_POST[tipo_banco]',"
                        . "'$_POST[subclave]',"
                        . "'$_POST[categoria]',"
                        . "'$_POST[txtdescripcion1]')";
  		
  		$result=sql_ejecutar($query);	
       $sql_codcon   = "SELECT max(codcon) as cant from nomconceptos";
        // echo $sql_codcon;exit();
        $res_codcon   = query($sql_codcon,$conexion);
        $cantidad     = fetch_array($res_codcon);
        $prestamo = $cantidad['cant']+1;
              
          $formula       = '$T01=CUOTAPRE_EXT($FICHA,$FECHANOMINA,$FECHAFINNOM,'.$codigo_nuevo.');$T02=SALDOPRE($FICHA,$FECHANOMINA,$FECHAFINNOM,0);$GASTOADMON = GASTOADMON($FICHA,'.$codigo_nuevo.'); $MONTO=$T01;';
          //Agrego los registro a conceptos
          $sql1 = "insert into nomconceptos (codcon, descrip, tipcon, unidad, ctacon, contractual, impdet, proratea, usaalter, descalter, formula, modifdef, markar, tercero, ccosto, codccosto, debcre, bonificable, htiempo, valdefecto, con_cu_cc, con_mcun_cc, con_mcuc_cc, con_cu_mccn, con_cu_mccc, con_mcun_mccn, con_mcuc_mccc, con_mcun_mccc, con_mcuc_mccn, nivelescuenta, nivelesccosto, semodifica, verref, vermonto, particular, montocero, ee, fmodif, aplicaexcel, descripexcel, ctacon1, carga_masiva, orden_reporte_contable)
      VALUES ({$prestamo}, '{$_POST[txtdescripcion]}', 'D', 'M', '640000000', '1', 'S', '0', '0', '', '{$formula}', '0', '0', '0', '{$codigo_nuevo}', '0', '0', '0', '0', '0.00', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '1', '0', '1', '0', '0', '0', 'DEDUC', '142.0.1.010.01.01.001', 'N', '0')";
        query($sql1,$conexion);

        //agrego registro a acumulados conceptos
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ('{$prestamo}', 'CON', 'S', '0')";
        query($sql2,$conexion);
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ('{$prestamo}', 'OD', 'S', '0')";
        query($sql2,$conexion);
        $sql2 = "INSERT INTO nomconceptos_acumulados (codcon ,cod_tac ,operacion ,ee) VALUES ('{$prestamo}', 'PRES', 'S', '0')";
        query($sql2,$conexion);

        //agrego registro a frecuencia conceptos
        $sql3 = "INSERT INTO nomconceptos_frecuencias (codcon , codfre , ee) VALUES ('{$prestamo}', '2', '0')";
        query($sql3,$conexion);
        $sql4 = "INSERT INTO nomconceptos_frecuencias (codcon , codfre , ee) VALUES ('{$prestamo}', '3', '0')";
        query($sql4,$conexion);

        //agrego registro a situaciones conceptos
        $sql5 = "INSERT INTO nomconceptos_situaciones (codcon , estado , ee) VALUES ('{$prestamo}', 'REGULAR', '0')";
        query($sql5,$conexion);
        $sql5 = "INSERT INTO nomconceptos_situaciones (codcon , estado , ee) VALUES ('{$prestamo}', 'Activo', '0')";
        query($sql5,$conexion);

        $consulta_codtip = "SELECT codtip from nomtipos_nomina group by codtip";
        $resultado_codtip=query($consulta_codtip,$conexion);
        while ($fetch_codtip=fetch_array($resultado_codtip)) 
        {
            //agrego registro a tiponomina conceptos
            $sql6 = "INSERT INTO nomconceptos_tiponomina (codcon , codtip , ee) VALUES ('{$prestamo}', {$fetch_codtip['codtip']}, '0')";
            query($sql6,$conexion);
        
        }
      

  		activar_pagina("prestamos.php");				
		}
	}
	else
        {// Si el registro_id es mayor a 0 se va a editar el registro actual		
		
		$query="select * from nomprestamos where codigopr=$registro_id";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$codigo=$row[codigopr];	
		$nombre=$row[descrip];
		$concepto=$row[formula];
		$subclave=$row[subclave];
                $id_categoria_acreedor=$row[id_categoria_acreedor];
                $nombre1=$row[descrip_corto];
                $codigo_proveedor = $row[codigo_proveedor];
                $ruta_destino=$row[ruta_destino];
                $cuenta_destino=$row[cuenta_destino];
                $producto_destino=$row[producto_destino];
                $tipo_banco=$row[ee];
	}	
		
	if ($op_tp==2){					
		
		$query="UPDATE nomprestamos "
                        . "set codigopr=$registro_id,
                            descrip='$_POST[txtdescripcion]',"
                        . "codigo_proveedor='$_POST[txtcodigoproveedor]',"
                        . "formula='$_POST[concepto]',"
                        . "subclave='$_POST[subclave]',"
                        . "id_categoria_acreedor='$_POST[categoria]',"
                        . "ruta_destino='$_POST[ruta_destino]',"
                        . "cuenta_destino='$_POST[cuenta_destino]',"
                        . "producto_destino='$_POST[producto_destino]',"
                        . "ee='$_POST[tipo_banco]',"
                        . "descrip_corto='$_POST[txtdescripcion1]' "
                        . "WHERE codigopr=$registro_id";	
		
		$result=sql_ejecutar($query);				
		activar_pagina("prestamos.php");										
					
	}	

?>
<body class="page-full-width"  marginheight="0">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                <?php    
                  if ($registro_id==0)
                  {
                   echo "Agregar Tipo Prestamos";
                  }
                  else
                  {
                   echo "Modificar Tipo Prestamos";
                  }
                ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='prestamos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            
        
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                <input name="op_tp" type="Hidden" id="op_tp" value="1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $registro_id; ?>">
                <input name="nombre_tabla" type="hidden" value="<?php echo $nombre_tabla; ?>">
                <div class="row">
                  <div class="col-md-3">C&oacute;digo:</div>
                  <div class="col-md-3">
                  <input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="txtdescripcion">C&oacute;digo Proveedor: </label></div>
                  <div class="col-md-6"><input class="form-control" name="txtcodigoproveedor" type="text" id="txtcodigoproveedor" value="<?php if ($registro_id!=0){ echo $codigo_proveedor; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="txtdescripcion">Descripci&oacute;n Corto:</label></div>
                  <div class="col-md-6"><input class="form-control" name="txtdescripcion1" type="text" id="txtdescripcion1" value="<?php if ($registro_id!=0){ echo $nombre1; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="txtdescripcion">Descripci&oacute;n:</label></div>
                  <div class="col-md-6"><input class="form-control" name="txtdescripcion" type="text" id="txtdescripcion" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="txtconcepto">Concepto Relacionado:</label></div>
                  <div class="col-md-6">
                      
                      <select name="concepto" id="concepto" class="form-control select2" >
                        <option value="0">Seleccione...</option>
                        <?php 
                          $query="SELECT codcon,descrip "
                               . "FROM nomconceptos "
                               . "WHERE descripexcel LIKE '%DEDUC%'";
                              //  . "WHERE codcon BETWEEN 501 and 599"; // Se quita por la nueva validación
                          $result=sql_ejecutar($query);
                          //ciclo para mostrar los datos
                          while ($row = fetch_array($result))
                          {     
                            // Opcion de modificar, se selecciona la situacion del registro a modificar   
                             if($row[codcon]==$concepto)
                             { 
                          ?>
                            <option value="<?php echo $row[codcon];?>" selected > <?php echo $row[codcon]." - ".$row[descrip];?> </option>
                            <?php 
                             }
                             else // opcion de agregar
                             { 
                            ?>
                             <option value="<?php echo $row[codcon];?>"><?php echo $row[codcon]." - ".$row[descrip];?></option>
                              <?php 
                             } 
                          }//fin del ciclo while
                        ?>
                      </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="subclave">SubClave:</label></div>
                  <div class="col-md-6"><input class="form-control" name="subclave" type="text" id="subclave" value="<?php if ($registro_id!=0){ echo $subclave; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="tipo_banco">Tipo Banco:</label></div>
                  <div class="col-md-6">
                      
                    <select name="tipo_banco" id="tipo_banco" class="form-control select2" >                       
                      <option value="0" <?php if($tipo_banco=="0") echo "selected" ;?>>Seleccione...</option>
                      <option value="1" <?php if($tipo_banco=="1") echo "selected" ;?>>BANCO</option>
                      <option value="2" <?php if($tipo_banco=="2") echo "selected" ;?>>FINANCIERA</option>
                  </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="txtconcepto">Categoria:</label></div>
                  <div class="col-md-6">
                      
                      <select name="categoria" id="categoria" class="form-control select2" >
                        <option value="0">Seleccione...</option>
                        <?php 
                        $query="SELECT * "
                             . "FROM categoria_acreedor ";
                        $result1=sql_ejecutar($query);
                      //ciclo para mostrar los datos
                        while ($row1 = fetch_array($result1))
                        {     
                          // Opcion de modificar, se selecciona la situacion del registro a modificar   
                           if($row1[id_categoria_acreedor]==$id_categoria_acreedor)
                           { 
                        ?>
                          <option value="<?php echo $row1[id_categoria_acreedor];?>" selected > <?php echo $row1[descripcion_categoria_acreedor];?> </option>
                          <?php 
                           }
                           else // opcion de agregar
                           { 
                          ?>
                           <option value="<?php echo $row1[id_categoria_acreedor];?>"><?php echo $row1[descripcion_categoria_acreedor];?></option>
                            <?php 
                           } 
                        }//fin del ciclo while
                            ?>
                  </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="subclave">Ruta Destino:</label></div>
                  <div class="col-md-6"><select name="ruta_destino" id="ruta_destino" class="form-control">
                                            <option value="0">Seleccione...</option>
                                            <option value="000000013" <?php if($ruta_destino=="000000013") echo "selected" ;?>>BANCO NACIONAL DE PANAMA</option>
                                            <option value="000001575" <?php if($ruta_destino=="000001575") echo "selected" ;?>>BANCO LAFISE PANAMÁ, S.A.</option>
                                            <option value="000001384" <?php if($ruta_destino=="000001384") echo "selected" ;?>>BAC INTERNATIONAL BANK</option>
                                            <option value="000001083" <?php if($ruta_destino=="000001083") echo "selected" ;?>>BANCO ALIADO</option>
                                            <option value="000001504" <?php if($ruta_destino=="000001504") echo "selected" ;?>>BANCO AZTECA</option>
                                            <option value="000001562" <?php if($ruta_destino=="000001562") echo "selected" ;?>>BANCO DELTA</option>
                                            <option value="000001724" <?php if($ruta_destino=="000001724") echo "selected" ;?>>BANCO FICOHSA PANAMA</option>
                                            <option value="000000071" <?php if($ruta_destino=="000000071") echo "selected" ;?>>BANCO GENERAL</option>
                                            <option value="000001517" <?php if($ruta_destino=="000001517") echo "selected" ;?>>BANCO PICHINCHA PANAMA</option>
                                            <option value="000001258" <?php if($ruta_destino=="000001258") echo "selected" ;?>>CANAL BANK</option>
                                            <option value="000001588" <?php if($ruta_destino=="000001588") echo "selected" ;?>>BANESCO</option>
                                            <option value="000001614" <?php if($ruta_destino=="000001614") echo "selected" ;?>>BANISI, S.A.</option>
                                            <option value="000001164" <?php if($ruta_destino=="000001164") echo "selected" ;?>>BANK OF CHINA</option>
                                            <option value="000001685" <?php if($ruta_destino=="000001685") echo "selected" ;?>>BALBOA BANK & TRUST</option>
                                            <option value="000001397" <?php if($ruta_destino=="000001397") echo "selected" ;?>>BCT BANK</option>
                                            <option value="000000518" <?php if($ruta_destino=="000000518") echo "selected" ;?>>BICSA</option>
                                            <option value="000002529" <?php if($ruta_destino=="000002529") echo "selected" ;?>>CACECHI</option>
                                            <option value="000000770" <?php if($ruta_destino=="000000770") echo "selected" ;?>>CAJA DE AHORROS</option>
                                            <option value="000001591" <?php if($ruta_destino=="000001591") echo "selected" ;?>>CAPITAL BANK</option>
                                            <option value="000000039" <?php if($ruta_destino=="000000039") echo "selected" ;?>>CITIBANK</option>
                                            <option value="000002532" <?php if($ruta_destino=="000002532") echo "selected" ;?>>COEDUCO</option>
                                            <option value="000002516" <?php if($ruta_destino=="000002516") echo "selected" ;?>>COOESAN</option>
                                            <option value="000002503" <?php if($ruta_destino=="000002503") echo "selected" ;?>>COOPEDUC</option>
                                            <option value="000005005" <?php if($ruta_destino=="000005005") echo "selected" ;?>>COOPERATIVA CRISTOBAL</option>
                                            <option value="000000712" <?php if($ruta_destino=="000000712") echo "selected" ;?>>COOPERATIVA DE PROFESIONALES</option>
                                            <option value="000002545" <?php if($ruta_destino=="000002545") echo "selected" ;?>>COOPEVE</option>
                                            <option value="000001106" <?php if($ruta_destino=="000001106") echo "selected" ;?>>CREDICORP BANK</option>
                                            <option value="000001151" <?php if($ruta_destino=="000001151") echo "selected" ;?>>GLOBAL BANK</option>
                                            <option value="000000026" <?php if($ruta_destino=="000000026") echo "selected" ;?>>BANISTMO S.A.</option>
                                            <option value="000001630" <?php if($ruta_destino=="000001630") echo "selected" ;?>>MERCANTIL BANK</option>
                                            <option value="000001067" <?php if($ruta_destino=="000001067") echo "selected" ;?>>METROBANK S.A.</option>
                                            <option value="000001478" <?php if($ruta_destino=="000001478") echo "selected" ;?>>MMG BANK</option>
                                            <option value="000000372" <?php if($ruta_destino=="000000372") echo "selected" ;?>>MULTIBANK</option>
                                            <option value="000001672" <?php if($ruta_destino=="000001672") echo "selected" ;?>>PRIVAL BANK</option>
                                            <option value="000000424" <?php if($ruta_destino=="000000424") echo "selected" ;?>>THE BANK OF NOVA SCOTIA</option>
                                            <option value="000001494" <?php if($ruta_destino=="000001494") echo "selected" ;?>>ST. GEORGES BANK</option>
                                            <option value="000000408" <?php if($ruta_destino=="000000408") echo "selected" ;?>>TOWERBANK</option>
                                            <option value="000001708" <?php if($ruta_destino=="000001708") echo "selected" ;?>>UNIBANK</option>
                                            <option value="000001740" <?php if($ruta_destino=="000001740") echo "selected" ;?>>ALLBANK</option>
                                            <option value="000001656" <?php if($ruta_destino=="000001656") echo "selected" ;?>>BBP BANK, S.A.</option>
                                            <option value="000005018" <?php if($ruta_destino=="000005018") echo "selected" ;?>>EDIOACC, R.L</option>
                                            <option value="000000916" <?php if($ruta_destino=="000000916") echo "selected" ;?>>BANCO DEL PACÍFICO (PANAMÁ), S.A.</option>
                                            <option value="000001805" <?php if($ruta_destino=="000001805") echo "selected" ;?>>ATLAS BANK</option>
                                            <option value="000005021" <?php if($ruta_destino=="000005021") echo "selected" ;?>>ECASESO</option>
                                            <option value="000005034" <?php if($ruta_destino=="000005034") echo "selected" ;?>>COOPRAC, R.L.</option>
                                            <option value="000000181" <?php if($ruta_destino=="000000181") echo "selected" ;?>>DAVIVIENDA</option>
                                    </select>
                  </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="subclave">Cuenta Destino:</label></div>
                  <div class="col-md-6"><input class="form-control" name="cuenta_destino" type="text" id="cuenta_destino" value="<?php if ($registro_id!=0){ echo $cuenta_destino; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="subclave">Producto Destino:</label></div>
                  <div class="col-md-6"><select name="producto_destino" id="producto_destino" class="form-control">
                                            <option value="0" <?php if($producto_destino=="0") echo "selected" ;?>>Seleccione...</option>
                                            <option value="04" <?php if($producto_destino=="04") echo "selected" ;?>>CUENTA DE AHORROS</option>
                                            <option value="03" <?php if($producto_destino=="03") echo "selected" ;?>>CUENTA CORRIENTE</option>
                                            <option value="03" <?php if($producto_destino=="03") echo "selected" ;?>>TARJETAS DE CREDITO Y PREPAGADAS</option>
                                            <option value="07 <?php if($producto_destino=="07") echo "selected" ;?>">PRESTAMOS</option>
                                    </select>
                  </div>
                </div>
                <br>
                <div class="row">
                &nbsp;
                
                </div>
                <div class="row">
                  <div class="col-md-offset-4 col-md-2"><?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?></div>
                  <div class="col-md-1"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   include ("../footer4.php");
 ?>
  <script type="text/javascript">
     $(document).ready(function(){

    $('#concepto').select2();
    $('#ruta_destino').select2();
    $('#producto_destino').select2();
    $('#categoria').select2();
});

 </script>
</body>
</html>


