<?php
session_start();
ini_set("display_errors", 1);

require_once("../../../libs/php/adodb5/adodb.inc.php");
require_once("../../../libs/php/configuracion/config.php");
require_once("../../../libs/php/clases/ConexionComun.php");
require_once("../../../libs/php/clases/ConexionAdmin.php");
require_once("../../../libs/php/clases/login.php");
require_once("../../../libs/php/clases/Menu.php");
require_once("../../../libs/php/clases/permisos.php");
#include_once("../../../libs/php/clases/compra.php"); 
include_once("../../../libs/php/clases/correlativos.php");
require_once "../../../libs/php/clases/numerosALetras.class.php";
include("../../../../menu_sistemas/lib/common.php");

if (isset($_GET["opt"]) == true || isset($_POST["opt"]) == true) {
    $conn = new ConexionComun();
    $login = new Login();
    $permisos = new Permisos();
    $opt = (isset($_GET["opt"])) ? $_GET["opt"] : $_POST["opt"];

    switch ($opt) {
        case "eliminar_asientoCXP":
            $instruccion = "SELECT * FROM cxp_edocuenta_detalle WHERE cod_edocuenta_detalle = '" . $_GET["cod"] . "'";
            $campos = $conn->ObtenerFilasBySqlSelect($instruccion);

            $instruccion = "delete from tabla_impuestos WHERE numero_control_factura = '" . $campos[0]["numero"] . "' and tipo_documento='c' and totalizar_monto_retencion='" . $campos[0]["monto"] . "'";
            $conn->Execute2($instruccion);

//,fecha_anulacion='".$_GET["fecha"]."',observacion_anulado='".$_GET["motivoAnulacion"]."'
            $instruccion = "update cxp_edocuenta_detalle set marca='',estado = '0',fecha_anulacion='" . $_GET["fecha"] . "',observacion_anulado='" . $_GET["motivoAnulacion"] . "' WHERE cod_edocuenta_detalle = '" . $_GET["cod"] . "'";
            $conn->Execute2($instruccion);

            $instruccion = "update cxp_edocuenta set marca = '' WHERE cod_edocuenta = " . $campos[0]["cod_edocuenta"];
            $conn->Execute2($instruccion);

            $instruccion = "delete from cxp_factura_pago WHERE cxp_edocuenta_detalle_fk = '" . $_GET["cod"] . "'";
            $conn->Execute2($instruccion);

            $instruccion = "delete from cxp_edocuenta_formapago WHERE cod_edocuenta_detalle = '" . $_GET["cod"] . "'";
            $conn->Execute2($instruccion);
//echo $instruccion;
            break;
        case "eliminar_asientoCXC":

            $instruccion = "SELECT * FROM cxc_edocuenta_detalle WHERE cod_edocuenta_detalle = '".$_GET["cod"]."'";
            $campos = $conn->ObtenerFilasBySqlSelect($instruccion);

            $instruccion = "delete from cxc_edocuenta_detalle WHERE cod_edocuenta_detalle = '".$_GET["cod"]."'";
            echo $conn->Execute2($instruccion);
            
            $instruccion = "update cxc_edocuenta set marca = '' WHERE cod_edocuenta = " . $campos[0]["cod_edocuenta"];
            echo $instruccion;
            $conn->Execute2($instruccion);

            $instruccion = "delete from tabla_impuestos WHERE numero_control_factura = '" . $campos[0]["numero"] . "' and tipo_documento='f' and totalizar_monto_retencion='" . $campos[0]["monto"] . "'";
            echo $conn->Execute2($instruccion);

            

            break;
        case "impuestos":
            $instruccion = "SELECT * FROM lista_impuestos as li
            left join formulacion_impuestos as fi on li.cod_formula=fi.cod_formula
            WHERE cod_impuesto= '" . $_GET["cod_impuesto"] . "'";
            $campos = $conn->ObtenerFilasBySqlSelect($instruccion);
            $PORCENTAJE = $campos[0]["alicuota"];
            $PAGOMAYORA = $campos[0]["pago_mayor_a"];
            $MONTOSUSTRACCION = $campos[0]["monto_sustraccion"];
            $MONTOBASE = $_GET["monto_base"];
            $formula = $campos[0]["formula"];
            $resultado = eval($formula);
            $calculo = $_GET["monto_islr"] * $porcentaje;
            echo "[{'total_retencion':'" . $MONTO . "','porcentaje':'" . $campos[0]["alicuota"] . "','formula':'" . $campos[0]["formula"] . "','resultado':'" . $MONTO . "','codigo_impuesto':'" . $campos[0]["cod_impuesto"] . "','cod_tipo_impuesto':'" . $campos[0]["cod_tipo_impuesto"] . "'}]";
            break;
        case "impuesto_iva":
            $instruccion = "SELECT * FROM impuesto_iva WHERE cod_impuesto_iva = " . $_GET["cod_impuesto_iva"];
            $campos = $conn->ObtenerFilasBySqlSelect($instruccion);
            $calculo = $_GET["montoiva"] * ($campos[0]["porcentaje"] / 100);
            echo "[{'total_iva':'" . ($calculo) . "','porcentaje':'" . $campos[0]["porcentaje"] . "'}]";
            break;
        case "ValidarCodigoitem":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM item WHERE cod_item = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : "-1";
            break;
        case "caja_egreso_persona_terceros":
            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;
            if ($busqueda) 
            {
                $id = (isset($_POST["id"])) ? $_POST["id"] : "";
                $codigo = (isset($_POST["codigo"])) ? $_POST["codigo"] : "";

                $descripcion = (isset($_POST["descripcion"])) ? $_POST["descripcion"] : "";

                if ($codigo != "") {
                    $andWHERE .= " upper(codigo) like upper('%" . $codigo . "%')";
                }

                if ($descripcion != "") {
                    if ($codigo != "") {
                        $andWHERE .= " and ";
                    } else {
                        $andWHERE = " ";
                    }
                    $andWHERE .= " upper(descripcion) like upper('%" . $descripcion . "%')";
                }

                $sql = "SELECT * FROM caja_egreso_persona_terceros WHERE " . $andWHERE;
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "SELECT * FROM caja_egreso_persona_terceros WHERE  " . $andWHERE . " limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            } else {
                $sql = "SELECT * FROM caja_egreso_persona_terceros";
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "SELECT * FROM caja_egreso_persona_terceros limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }

            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes1),
                "data" => $campos_comunes
            ));
            break;
        case "filtroProveedores":  
            /**
             * Procedimiento de busqueda de filtroProveedores
             *
             * Realizado por:
             * Luis E. Viera Fernandez
             *
             * Correo:
             *      levieraf@gmail.com
             *
             */

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;

            if ($busqueda) {

                // Filtro para los proveedores
                $id_proveedor = (isset($_POST["id_proveedor"])) ? $_POST["id_proveedor"] : "";
                $rif = (isset($_POST["ciruc"])) ? $_POST["ciruc"] : "";
                $cod_proveedor = (!isset($_POST["cod_proveedor"])) ? "" : $_POST["cod_proveedor"];
                $nombre = (!isset($_POST["proveedor"])) ? "" : $_POST["proveedor"];

                if ($cod_proveedor != "") {
                    $andWHERE .= " and upper(cod_proveedor) like upper('%" . $cod_proveedor . "%')";
                }

                if ($nombre != "") {
                    if ($cod_proveedor != "") {
                        $andWHERE .= " and ";
                    } else {
                        $andWHERE = " and ";
                    }
                    $andWHERE .= " upper(descripcion) like upper('%" . $nombre . "%')";
                }

                $sql = "SELECT * FROM proveedores WHERE estatus = 'A' " . $andWHERE;

                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);

                $sql = "SELECT * FROM proveedores WHERE estatus = 'A' " . $andWHERE . " limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            } else {
                $sql = "SELECT * FROM proveedores WHERE estatus = 'A'";
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "SELECT * FROM proveedores WHERE estatus = 'A' limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }

            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes1),
                "data" => $campos_comunes
            ));
            break;


       case "filtroClientes":  
            /**
             * Procedimiento de busqueda de Clientes
             *
             * Realizado por:
             * Luis E. Viera Fernandez
             *
             * Correo:
             *      levieraf@gmail.com
             *
             */

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;

            if ($busqueda) {

                // Filtro para los cliente
                $id_cliente = (isset($_POST["id_cliente"])) ? $_POST["id_cliente"] : "";
                $rif = (isset($_POST["ciruc"])) ? $_POST["ciruc"] : "";
                $cod_cliente = (!isset($_POST["cod_cliente"])) ? "" : $_POST["cod_cliente"];
                $nombre = (!isset($_POST["cliente"])) ? "" : $_POST["cliente"];

                $andWHERE = " and ";
                if ($rif != "") {
                    $andWHERE .= " upper(rif) like upper('%" . $rif . "%')";
                }

                if ($cod_cliente != "") {
                    $andWHERE .= " upper(cod_cliente) like upper('%" . $cod_cliente . "%')";
                }

                if ($nombre != "") {
                    if ($cod_cliente != "") {
                        $andWHERE .= " and ";
                    } else {
                        $andWHERE = " and ";
                    }
                    $andWHERE .= " upper(nombre) like upper('%" . $nombre . "%')";
                }

                if ($rif != "") {
                    if ($nombre != "" || $cod_cliente != "") {
                        $andWHERE .= " and ";
                    } else {
                        $andWHERE = " and ";
                    }
                    $andWHERE .= " upper(rif) like upper('%" . $rif . "%')";
                }

                if ($rif == "" && $cod_cliente == "" && $nombre == "") {
                    $andWHERE = "";
                }

                $sql = "SELECT * FROM clientes WHERE estado = 'A' " . $andWHERE;
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);

                $sql = "SELECT * FROM clientes WHERE estado = 'A' " . $andWHERE . " limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            } else {
                $sql = "SELECT * FROM clientes WHERE estado = 'A'";
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "SELECT * FROM clientes WHERE estado = 'A' limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }

            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes1),
                "data" => $campos_comunes
            ));
            break;

        case "DetalleCliente":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM clientes WHERE id_cliente = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : json_encode($campos);
            break;
        case "Detalleproveedor":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM proveedor WHERE id_proveedor = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : json_encode($campos);
            break;
        case "ValidarCodigoCliente":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM clientes WHERE cod_cliente = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : "-1";
            break;
        case "ValidarCodigoVendedor":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vendedor WHERE cod_vendedor = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : "-1";
            break;
        case "ValidarNombreUsuario":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM usuarios WHERE usuario = '" . $_GET["v1"] . "'");
            echo (count($campos) == 0) ? "1" : "-1";
            break;
        case "Selectitem":
#$campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `item` AS i INNER JOIN `item_existencia_almacen` AS ie ON i.id_item = ie.id_item WHERE i.cod_item_forma` = '" . $_GET["v1"] . "' AND i.estatus = 'A' AND ie.cantidad>0");
 $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `item` WHERE `cod_item_forma` = '" . $_GET["v1"] . "' and estatus = 'A' order by referencia asc");
//$campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `item` WHERE `cod_item_forma` = '" . $_GET["v1"] . "' and estatus = 'A' order by descripcion1 asc");
//SELECT * FROM `item` as i left join compra as c on c.id_proveedor=6 left join compra_detalle as cd on c.id_compra=cd.id_compra WHERE i.cod_item_forma = 1 and i.id_item)
            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
            break;
        case "Selectitemporproveedor":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `item` WHERE `cod_item_forma` = '" . $_GET["v1"] . "' and estatus = 'A' order by descripcion1 asc");
//SELECT * FROM `item` as i left join compra as c on c.id_proveedor=6 left join compra_detalle as cd on c.id_compra=cd.id_compra WHERE i.cod_item_forma = 1 and i.id_item)
            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
            break;
        case "DetalleSelectitem":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `item` WHERE `id_item` = '" . $_GET["v1"] . "'");
            echo $campos;
            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
        break;
        case "getDepartamentos": //Rubros
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `departamentos`");
            
            echo json_encode(array(
                "success" => true,
                "total" => count($campos),
                "data" => $campos
            ));
            break;
        case "getGrupos": //SubRubros
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `grupo`");
            
            echo json_encode(array(
                "success" => true,
                "total" => count($campos),
                "data" => $campos
            ));
            break;
        case "getLineas": // MARCAS 
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM `linea`");
            
            echo json_encode(array(
                "success" => true,
                "total" => count($campos),
                "data" => $campos
            ));
            break;
        case "CargarAlmacenesDisponiblesByIdItem":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vw_existenciabyalmacen WHERE id_item = '" . $_GET["v1"] . "' and cantidad > 0 order by cod_almacen");
            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
            break;
        case "ExistenciaProductoAlmacenDefaultByIdItem":
            $cod_almacen = (isset($_GET["cod_almacen"])) ? $_GET["cod_almacen"] : "";

            if($cod_almacen){
                 $campos = $conn->ObtenerFilasBySqlSelect("SELECT almaexi.* FROM vw_existenciabyalmacen almaexi JOIN parametros_generales pg ON pg.cod_almacen = almaexi.cod_almacen WHERE id_item = '" . $_GET["v1"] . "' and cantidad > 0");
            }else{
                 $campos = $conn->ObtenerFilasBySqlSelect("SELECT almaexi.* FROM vw_existenciabyalmacen almaexi JOIN parametros_generales pg ON pg.cod_almacen = almaexi.cod_almacen WHERE id_item = '" . $_GET["v1"] . "' and cantidad > 0");
            }

            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
            break;

        case "verificarExistenciaItemByAlmacen":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vw_item_precomprometidos WHERE id_item = '" . $_GET["v2"] . "' and cod_almacen = '" . $_GET["v1"] . "'");
            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                echo json_encode($campos);
            }
            break;
        case "precomprometeritem":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vw_item_precomprometidos
            WHERE id_item = '" . $_GET["v1"] . "' and cod_almacen = '" . $_GET["codalmacen"] . "'");

            $cantidadExistenteOLD = $campos[0]["cantidad"];
            $cantidadPedidad = $_GET["cpedido"];

            $cantidadExistenteNEW = $cantidadExistenteOLD - $cantidadPedidad;
            if ($cantidadExistenteNEW < 0) {
                echo "[{'id':'-99','observacion':'La cantidad Pedida es mayor a la existente.'}]";
                exit;
            }
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM item WHERE id_item = " . $_GET["v1"] . " and cod_item_forma = 1"); // 1: item producto
            if (count($campos) > 0) {
//if(strcmp($_GET["tipo_transaccion"],"presupuesto")){
#echo $_GET["tipo_transaccion"];exit;
               $sql = "INSERT INTO item_precompromiso (
                        `id_item_precomiso`, `cod_item_precompromiso`, `id_item`, `cantidad`, `usuario_creacion`,
                        `fecha_creacion`, `idSessionActualphp`, `almacen`)
                        VALUES (
                        NULL , '" . $_GET["codprecompromiso"] . "','" . $_GET["v1"] . "', '" . $_GET["cpedido"] . "', '" . $login->getUsuario() . "',
                        CURRENT_TIMESTAMP , '" . $login->getIdSessionActual() . "','" . $_GET["codalmacen"] . "');";
                $conn->Execute2($sql);
                echo "[{'id':'1','observacion':''}]";
//}
            } else {
                echo "[{'id':'-1','observacion':''}]";
            }
            break;
        case 'seleccionarPedidoPendiente':
            /*$sql = "SELECT pd.*,
            pg.transporte_salida,pg.transporte,pg.empaques,pg.seguro,pg.flete,pg.comisiones,pg.manejo,pg.otros,pg.total_fob_gasto, 
            pfs.observacion observacion_salida  
            FROM pedido_detalle pd 
            left join pedido_formato_salida pfs on pfs.id_pedido = pd.id_pedido 
            left join pedido_detalle_gasto pg on pg.id_pedido = pfs.id_pedido  
            inner join item i on i.id_item = pd.id_item 
            WHERE pd.id_pedido = {$_GET["id_pedido"]};";*/
            $sql = "SELECT pd.*,i.referencia,i.unidad_empaque,i.cantidad_bulto,i.costo_actual
            FROM pedido_detalle pd 
            inner join item i on i.id_item = pd.id_item 
            WHERE pd.id_pedido = {$_GET["id_pedido"]};";
            $pedido_detalles = $conn->ObtenerFilasBySqlSelect($sql);
            $i=0;
            $registros_pedido_detalles = array();
            foreach ($pedido_detalles as $pedido_detalle) {
                $talla_color= "";
                $registros_pedido_detalles []= $pedido_detalle;
                if($pedido_detalle["_posee_talla_color"]=="si"){
                    $id_detalle_pedido = $pedido_detalle["id_detalle_pedido"];
                    $sql = "SELECT ptc.* FROM pedido_detalle_cantidad_talla_color ptc WHERE ptc.id_detalle_pedido = {$id_detalle_pedido};";
                    $talla_color = $conn->ObtenerFilasBySqlSelect($sql);
                    $registros_pedido_detalles[$i]["posee_talla_color"] = "si";
                    $registros_pedido_detalles[$i]["cantidad_por_talla_y_color"] = json_encode($talla_color);
                }else{
                    $registros_pedido_detalles[$i]["posee_talla_color"] = "no";
                }
                $i++;
            }

            $sql = "SELECT * FROM pedido_gasto pg where pg.id_pedido = {$_GET["id_pedido"]};";
            $gastos = $conn->ObtenerFilasBySqlSelect($sql);

            $sql = "SELECT pfs.observacion observacion_salida,p.termino_pago_id ,
            pfs.tipo tipo_salida,pfs.id_cliente cliente_salida,cl.nombre nombre_cliente_salida,
            pfs.via via_salida,pfs.marca marca_salida  
            FROM pedido_formato_salida pfs 
            inner join pedido p on p.id_pedido = pfs.id_pedido  
            left join clientes cl on cl.id_cliente = pfs.id_cliente 
            WHERE pfs.id_pedido = {$_GET["id_pedido"]};";
            $salida = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode(array(
                "gastos" => $gastos[0],
                "formato_salida" => $salida[0],
                "productos" => $registros_pedido_detalles
                ));
            break;
        case 'seleccionarNotaEntregaPendiente':
            header("Content-Type: text/plain");
            $sql = "SELECT * FROM nota_entrega_detalle WHERE id_nota_entrega = {$_GET["id_nota"]};";
            $campos = $conn->ObtenerFilasBySqlSelect($sql);
            echo json_encode($campos);
            break;
        case 'seleccionarCotizacionPendiente':
            header("Content-Type: text/plain");
            $campos = $conn->ObtenerFilasBySqlSelect("select id_divisa,fecha_creacion,ivaTotalCotizacion from cotizacion_presupuesto where id_cotizacion='{$_GET["id_cotizacion"]}'");
            $fecha_creacion = $campos[0]['fecha_creacion'];
            $id_divisa = $campos[0]['id_divisa'];
            $sql = "SELECT p.formapago,p.termino_pago_id,d.id_cotizacion_presupuesto_detalle,ifnull(ie.cantidad,0) _item_cantidad_existencia,p.observacion,p.termino_pago_id,d._item_piva iva,i.cubi4,i.cubi5,i.kilos_bulto,i.referencia referencia_item,i.costo_actual,i.cantidad_bulto,i.cod_item,i.unidad_empaque,d._item_cantidad,truncate( coalesce(t.tasa,1.00),2) tasa_cambio,d.* 
             FROM cotizacion_presupuesto_detalle d 
              inner join item i on i.id_item = d.id_item 
              inner join cotizacion_presupuesto p on p.id_cotizacion=d.id_cotizacion 
              left join item_existencia_almacen ie on ie.id_item = d.id_item and ie.cod_almacen = '".$_GET['almacen'] ."'
              left join (select t1.* from tasas_cambio t1 where t1.divisa='".$id_divisa."' and t1.fecha <= '".$fecha_creacion."' order by t1.fecha desc limit 1) t on t.divisa = p.id_divisa
              WHERE d.id_cotizacion = '{$_GET["id_cotizacion"]}';";
            $campos = $conn->ObtenerFilasBySqlSelect($sql);
            echo json_encode($campos);
            break;
        /* case 'generarCuotas':

          header("Content-Type: text/plain");

          $correlativos = new Correlativos();
          $cuotas = $_POST["cuota"];
          $cuota_precios = $_POST["precio"];
          $cuota_anios = $_POST["anio"];
          $cuota_meses = $_POST["mes"];

          $conn->Execute2("UPDATE `cuota` SET `estatus` = 1 WHERE `id` = {$_POST["id_cuota"]};");

          foreach ($cuotas as $key => $cuota_descripcion) {//Pendiente de quitar el 1 para `cuota_cuota_id` y eliminar dicho campo de la tabla

          $codigo = $correlativos->getUltimoCorrelativo("cod_producto", 1, "si", "C");
          $conn->ExecuteTrans("UPDATE correlativos SET contador = contador + 1 WHERE campo = 'cod_producto';");

          $cuota = $conn->ObtenerFilasBySqlSelect("SELECT descripcion FROM cuota WHERE id = {$_POST["id_cuota"]};");

          //$cuota_descripcion = $cuota[0]["descripcion"] . " " . $cuota_anios[$key] . "-" . ($cuota_meses[$key] < 10 ? "0" . $cuota_meses[$key] : $cuota_meses[$key]);

          $instruccion = "INSERT INTO `item`(
          `cod_item`, `costo_actual`, `descripcion1`,
          `precio1`, `utilidad1`, `coniva1`, `precio2`, `utilidad2`,
          `coniva2`, `precio3`, `utilidad3`, `coniva3`, `existencia_min`,
          `existencia_max`, `monto_exento`, `iva`,
          `estatus`,`usuario_creacion`, `fecha_creacion`, `cod_item_forma`, unidad_empaque,
          costo_promedio, costo_anterior)
          VALUES(
          '{$codigo}', '{$cuota_precios[$key]}', '{$cuota_descripcion}',
          '{$cuota_precios[$key]}', '0', '{$cuota_precios[$key]}', '{$cuota_precios[$key]}',
          '0', '{$cuota_precios[$key]}', '{$cuota_precios[$key]}', '0',
          '{$cuota_precios[$key]}', '0',  '0', '0', '0',
          'I', '" . $login->getUsuario() . "', CURRENT_TIMESTAMP, 4, NULL,
          '{$cuota_precios[$key]}', '{$cuota_precios[$key]}');";

          $conn->ExecuteTrans($instruccion);

          $id_item = $conn->getInsertID();

          $instruccion = "INSERT INTO `cuota_mes` (`id`, `cuota_id`, `cuota_cuota_id`, `id_item`, `precio`, `mes`, `anio`)
          VALUES (NULL, {$_POST["id_cuota"]}, 1, {$id_item}, {$cuota_precios[$key]}, $cuota_meses[$key], {$cuota_anios[$key]});";
          $campos = $conn->Execute2($instruccion);
          //echo (count($campos) == 0) ? "1" : "-1";
          }
          break; */
        case 'ponerCuotaPagada':
            header("Content-Type: text/plain");
            /* Aqui debo recibir un array con todas las cuotas seleeccionadas en la pantalla de facturacion */
            $cuotas = json_decode($_POST["cuotas"]);
            $cliente = json_decode($_POST["cliente"]);
            foreach ($cuotas as $cuota) {
#$conn->Execute2("UPDATE `cuota_cliente` SET estatus = 1 WHERE id = {$cuota};");
                $seleccionadas .= $cuota . ",";
            }
            $seleccionadas = trim($seleccionadas, ",");
//$campos = $conn->ObtenerFilasBySqlSelect("SELECT cc.id, cc.id_cuota_generada, cc.id_cliente, cm.cuota_id, cm.precio AS precio, cm.mes AS mes, cm.anio AS anio, c.descripcion FROM cuota_cliente AS cc INNER JOIN cuota_mes AS cm ON cc.id_cuota_generada = cm.id INNER JOIN cuota AS c ON cm.cuota_id = c.id WHERE cc.id_cliente = {$cliente} AND cc.estatus = 0 AND cc.id IN ({$seleccionadas});");
            $campos = $conn->ObtenerFilasBySqlSelect("
                    SELECT i.cod_item, cc.id, cc.id_cuota_generada, cc.id_cliente, cm.cuota_id, cm.id_item, cm.precio AS precio, cm.mes AS mes, cm.anio AS anio, c.descripcion
                    FROM cuota_cliente AS cc
                    INNER JOIN cuota_mes AS cm ON cc.id_cuota_generada = cm.id
                    INNER JOIN cuota AS c ON cm.cuota_id = c.id
                    INNER JOIN item AS i ON i.id_item = cm.id_item
                    WHERE cc.id_cliente = {$cliente} AND cc.estatus = 0 AND cc.id IN ({$seleccionadas});");
            echo json_encode($campos);
            break;
        /* case "asignarCuotas":
          $clientes_existentes = $conn->ObtenerFilasBySqlSelect("SELECT id_cliente, fdeuda FROM clientes WHERE cod_tipo_cliente = 1 --Contadores;");
          $datacuota = $conn->ObtenerFilasBySqlSelect("SELECT id, mes, anio FROM cuota_mes WHERE cuota_id = {$_GET["id_cuota"]} AND estado = 0 -- No asignada");

          $cant = count($datacuota);

          foreach ($clientes_existentes as $cli) {
          $fdeuda = explode("-", $cli["fdeuda"]);
          if ($cli["fdeuda"] != "0000-00-00") {
          $asignar = false;
          foreach ($datacuota as $cuota) {
          if ($fdeuda[0] == $cuota["anio"] && $fdeuda[1] == $cuota["mes"]) {
          $asignar = true;
          }
          if ($asignar) {
          $conn->Execute2("INSERT INTO `cuota_cliente` (`id_cuota_generada`, `id_cliente`) VALUES ({$cuota["id"]}, {$cli["id_cliente"]});");
          }
          }
          }
          $conn->ExecuteTrans("UPDATE cuota_mes SET estado = 1 WHERE cuota_id = {$_GET["id_cuota"]};");
          }
          //$cuotas = $conn->Execute2("UPDATE `cuota` SET `estatus` = 2 WHERE `id` = {$_GET["id_cuota"]};");
          echo json_encode(array(
          "success" => true,
          "total" => $cant, //count($cuotas),
          "data" => $cuotas
          ));
          break; */
        case 'tipoFacturacion':
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT tipo_facuracion FROM parametros_generales");
            echo json_encode($campos);
            break;
        case "delete_precomprometeritem":
            $sql = "delete from item_precompromiso
                    WHERE cod_item_precompromiso = '" . $_GET["codprecompromiso"] . "'  and
                    idSessionActualphp = '" . $login->getIdSessionActual() . "'      and
                    usuario_creacion = '" . $login->getUsuario() . "' and id_item = '" . $_GET["v1"] . "'";
            $conn->Execute2($sql);
            break;
        case "det_edocuentacxp":
            $data_parametros = $conn->ObtenerFilasBySqlSelect("SELECT * FROM parametros_generales");
            foreach ($data_parametros as $key => $lista) {
                $valueSELECT[] = $lista["cod_empresa"];
                $outputidfiscalSELECT[] = $lista["moneda"];
            }
            $campos = $conn->ObtenerFilasBySqlSelect("
                SELECT *, vw_cxp.numero AS num_cdet, cxp_edocuenta.vencimiento_persona_contacto, cxp_edocuenta.vencimiento_telefono, cxp_edocuenta.vencimiento_descripcion
                FROM vw_cxp
                INNER JOIN cxp_edocuenta ON cxp_edocuenta.cod_edocuenta = vw_cxp.cod_edocuenta
                WHERE vw_cxp.cod_edocuenta = " . $_GET["cod_edocuenta"]);
            if (count($campos) == 0) {
                exit;
            }
            echo '<tr class="edocuenta_detalle">
          <td colspan="8">
            <div style=" background-color:#f3ed8b; border: 1px solid #ededed; border-radius: 7px; padding:1px; margin-top:0.3%; margin-bottom: 10px; padding-bottom: 7px; margin-left: 10px; font-size: 13px;">
                <table >
                    <thead>
                        <th style="border-bottom: 1px solid #949494;width:110px;">ID</th>
                        <th style="border-bottom: 1px solid #949494;width:110px;">Documento</th>
                        <th style="border-bottom: 1px solid #949494;">N&uacute;mero</th>
                        <th style="border-bottom: 1px solid #949494;width:120px;">Fecha Emisi&oacute;n</th>
                        <th align="justify" style="border-bottom: 1px solid #949494;width:300px;">Descripci&oacute;n</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Abonos/Pagos</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Deuda</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Opt</th>
                    </thead>
                    <tbody>';

            $acuDebitos = 0;
            $acuCreditos = 0;
            foreach ($campos as $key => $item) {
                if ($item["estado"] <> '0') {
                    echo '
                        <tr>
                            <td align="center" style="border-bottom: 1px solid #949494;width:110px;">' . $item["cod_edocuenta_detalle"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:110px;">' . $item["documento_cdet"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;">' . $item["num_cdet"] . '</td>
                            <td align="center" style="border-bottom: 1px solid #949494;width:120px;">' . $item["fecha_emision_edodet"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:300px;">' . $item["descripcion"] . '</td>
                            <td align="right" style="border-bottom: 1px solid #949494;width:110px;">' . number_format($item['debito'], 2, ",", ".") . ' ' . $lista["moneda"] . ' </td>
                            <td align="right" style="border-bottom: 1px solid #949494;">' . number_format($item['credito'], 2, ",", ".") . ' ' . $lista["moneda"] . ' </td>
                            <td align="right" style="border-bottom: 1px solid #949494;">';

                    if ($key > 0) {
                        echo "<input type='hidden' id='detalle_asiento' name='detalle_asiento' value='" . $item["cod_edocuenta_detalle"] . "'>";
                        echo '<img onclick="javascript: guardarr(' . $item["cod_edocuenta_detalle"] . ')" style="cursor:pointer;" title="Eliminar Asiento" src="../../libs/imagenes/cancel.gif">';
                    }

                    echo '</td>
        </tr>';
                }
                $acuDebitos += $item['debito'];
                $acuCreditos += $item['credito'];
            }
            echo '
                        <tr>
                            <td colspan="8" align="right" style="border-bottom: 1px solid #949494;width:300px;"></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right" style="border-bottom: 1px solid #949494;width:300px;"><b>Total Pagos,Abonos/Deudas:</b></td>
                            <td align="right" style="border-bottom: 1px solid #949494;"><b>' . number_format($acuDebitos, 2, ",", ".") . ' ' . $lista["moneda"] . '</b></td>
                            <td align="right" style="border-bottom: 1px solid #949494;"><b>' . number_format($acuCreditos, 2, ",", ".") . ' ' . $lista["moneda"] . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right" style="border-bottom: 1px solid #949494;width:300px;"><b>Saldo Pendiente:</b></td>
                            <td colspan="2"align="right" style="border-bottom: 1px solid #949494;"><b style="color:red;">' . number_format($acuCreditos - $acuDebitos, 2, ",", ".") . ' ' . $lista["moneda"] . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="8" align="right" style="border-bottom: 1px solid #949494;">

                            </td>
                        </tr>
    ';

            if ($campos[0]["marca"] != "X") {
                echo '

                        <tr>
                            <td colspan="6" style="text-align: left; border-bottom: 1px solid #949494;">

                            <table style="cursor: pointer;" align="right" class="btn_bg" onClick="javascript:window.location=\'?opt_menu=89&opt_seccion=88&opt_subseccion=pagoabonoCXP&cod=' . $_GET["codigo_proveedor"] . '&cod2=' . $_GET["cod_edocuenta"] . '\'" name="buscar" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0px;" align="right"><img src="../../libs/imagenes/bt_left.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                    <td class="btn_bg"><img src="../../libs/imagenes/factu.png" width="16" height="16" /></td>
                                    <td class="btn_bg" nowrap style="padding: 0px 1px;">Agregar Pago/Abono</td>
                                    <td  style="padding: 0px;" align="left"><img  src="../../libs/imagenes/bt_right.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                </tr>
                            </table>
                        </td>
			<td colspan="7" style="text-align: left; border-bottom: 1px solid #949494;">

                            <table style="cursor: pointer;" align="right" class="btn_bg" onClick="javascript:window.location=\'?opt_menu=89&opt_seccion=88&opt_subseccion=facturasCXP&cod=' . $_GET["codigo_proveedor"] . '&cod2=' . $_GET["cod_edocuenta"] . '\'" name="buscar" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0px;" align="right"><img src="../../libs/imagenes/bt_left.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                    <td class="btn_bg"><img src="../../libs/imagenes/list.gif" width="16" height="16" /></td>
                                    <td class="btn_bg" nowrap style="padding: 0px 1px;">Facturas/Notas de Credito</td>
                                    <td  style="padding: 0px;" align="left"><img  src="../../libs/imagenes/bt_right.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                </tr>
                            </table>
                        </td>
                        </tr>
        ';
            }
            if ($campos[0]["marca"] == "X") {
                echo '

                        <tr>
                            <td colspan="6" style="text-align: left; border-bottom: 1px solid #949494;">
                        </td>
			<td colspan="7" style="text-align: left; border-bottom: 1px solid #949494;">

                            <table style="cursor: pointer;" align="right" class="btn_bg" onClick="javascript:window.location=\'?opt_menu=85&opt_seccion=88&opt_subseccion=facturasCXP&cod=' . $_GET["codigo_proveedor"] . '&cod2=' . $_GET["cod_edocuenta"] . '\'" name="buscar" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0px;" align="right"><img src="../../libs/imagenes/bt_left.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                    <td class="btn_bg"><img src="../../libs/imagenes/list.gif" width="16" height="16" /></td>
                                    <td class="btn_bg" nowrap style="padding: 0px 1px;">Facturas/Notas de Credito</td>
                                    <td  style="padding: 0px;" align="left"><img  src="../../libs/imagenes/bt_right.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                </tr>
                            </table>
                        </td>
                        </tr>
        ';
            }


            echo
            '</tbody>
    </table>
    </div>
    </td>
    </tr>';




            break;

        case "det_edocuenta":
            $data_parametros = $conn->ObtenerFilasBySqlSelect("SELECT * FROM parametros_generales");
            foreach ($data_parametros as $key => $lista) {
                $valueSELECT[] = $lista["cod_empresa"];
                $outputidfiscalSELECT[] = $lista["moneda"];
            }
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT *
,vw_cxc.numero as num_cdet
,cxc_edocuenta.vencimiento_persona_contacto,
cxc_edocuenta.vencimiento_telefono,
cxc_edocuenta.vencimiento_descripcion from vw_cxc
 inner join cxc_edocuenta on cxc_edocuenta.cod_edocuenta = vw_cxc.cod_edocuenta
WHERE vw_cxc.cod_edocuenta = " . $_GET["cod_edocuenta"]);
            if (count($campos) == 0) {
                exit;
            }
            echo '<tr class="edocuenta_detalle">
          <td colspan="8">
            <div  style=" background-color:#fdfdfd; border: 1px solid #ededed;-moz-border-radius: 7px;padding:1px; margin-top:0.3%; margin-bottom: 10px;padding-bottom: 7px;margin-left: 10px;  font-size: 13px; ">
                <table >
                    <thead>
                        <th style="border-bottom: 1px solid #949494;width:110px;">ID</th>
                        <th style="border-bottom: 1px solid #949494;width:110px;">Documento</th>
                        <th style="border-bottom: 1px solid #949494;">N&uacute;mero</th>
                        <th style="border-bottom: 1px solid #949494;width:120px;">Fecha Emisi&oacute;n</th>
                        <th align="justify" style="border-bottom: 1px solid #949494;width:300px;">Descripci&oacute;n</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Deuda</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Pago/Abono</th>
                        <th align="right" style="border-bottom: 1px solid #949494;width:110px;">Opt</th>
                    </thead>
                    <tbody>';


            $acuDebitos = 0;
            $acuCreditos = 0;
            foreach ($campos as $key => $item) {
                echo '
                        <tr>
                            <td align="center" style="border-bottom: 1px solid #949494;width:110px;">' . $item["cod_edocuenta_detalle"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:110px;">' . $item["documento_cdet"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;">' . $item["num_cdet"] . '</td>
                            <td align="center" style="border-bottom: 1px solid #949494;width:120px;">' . $item["fecha_emision_edodet"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:300px;">' . $item["descripcion"] . '</td>
                            <td align="right" style="border-bottom: 1px solid #949494;">' . number_format($item['debito'], 2, ",", ".") . ' ' . $lista["moneda"] . '</td>
                            <td align="right" style="border-bottom: 1px solid #949494;">' . number_format($item['credito'], 2, ",", ".") . ' ' . $lista["moneda"] . '</td>
                            <td align="right" style="border-bottom: 1px solid #949494;">';
//if($item['debito']=="0.00"){
                if ($key > 0) {
                    echo '<img class="eliminarAsiento"  style="cursor:pointer;" title="Eliminar Asiento" src="../../libs/imagenes/cancel.gif">';
                    echo "<input type='hidden' id='detalle_asiento' name='detalle_asiento' value='" . $item["cod_edocuenta_detalle"] . "'>";
                }

                echo '</td>
        </tr>';

                $acuDebitos += $item['debito'];
                $acuCreditos += $item['credito'];
            }
            echo '
                        <tr>
                            <td colspan="8" align="right" style="border-bottom: 1px solid #949494;width:300px;"></td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right" style="border-bottom: 1px solid #949494;width:300px;"><b>Total Deudas,Pagos/Abonos:</b></td>
                            <td align="right" style="border-bottom: 1px solid #949494;"><b>' . number_format($acuDebitos, 2, ",", ".") . ' ' . $lista["moneda"] . '</b></td>
                            <td align="right" style="border-bottom: 1px solid #949494;"><b>' . number_format($acuCreditos, 2, ",", ".") . '  ' . $lista["moneda"] . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="4" align="right" style="border-bottom: 1px solid #949494;width:300px;"><b>Saldo Pendiente:</b></td>
                            <td colspan="2"align="right" style="border-bottom: 1px solid #949494;"><b style="color:red;">' . number_format($acuDebitos - $acuCreditos, 2, ",", ".") . '  ' . $lista["moneda"] . '</b></td>
                        </tr>
                        <tr>
                            <td colspan="7" align="right" style="border-bottom: 1px solid #949494;width:300px;">

                            </td>
                        </tr>
    ';


            if ($campos[0]["marca"] != "X") {
                echo '<tr>
                            <td colspan="6" style="text-align: left; border-bottom: 1px solid #949494;width:110px;">
                            <table style="cursor: pointer;" align="right" class="btn_bg" onClick="javascript:window.location=\'?opt_menu=5&opt_seccion=59&opt_subseccion=pagooabono&cod=' . $_GET["codigo_cliente"] . '&cod2=' . $_GET["cod_edocuenta"] . '\'" name="buscar" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0px;" align="right"><img src="../../libs/imagenes/bt_left.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                    <td class="btn_bg"><img src="../../libs/imagenes/factu.png" width="16" height="16" /></td>
                                    <td class="btn_bg" nowrap style="padding: 0px 1px;">Agregar Pago/Abono</td>
                                    <td  style="padding: 0px;" align="left"><img  src="../../libs/imagenes/bt_right.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                </tr>
                            </table>
                            <br>
                            <img src="../../libs/imagenes/ico_user.gif"> Persona de Contacto: ' . $campos[0]["vencimiento_persona_contacto"] . '<br>
                            <img src="../../libs/imagenes/ico_cel.gif"> Telefono de Contacto: ' . $campos[0]["vencimiento_telefono"] . '<br>
                            <img src="../../libs/imagenes/ico_view.gif"> ObservaciÃ³n: ' . $campos[0]["vencimiento_descripcion"] . '<br>
                            <img src="../../libs/imagenes/ew_calendar.gif"> Fecha de Vencimiento: ' . $campos[0]["vencimiento_fecha"] . '<br>

                        </td>
                        </tr>
        ';
            }
            echo '</tbody></table></div></td></tr>';
            break;
        case "det_items":
            if ($_GET["id_tipo_movimiento_almacen"] == '3' || $_GET["id_tipo_movimiento_almacen"] == '1') {
                $operacion = "Entrada";
                $campos = $conn->ObtenerFilasBySqlSelect("SELECT *,kad.cantidad AS cantidad_item,kad.precio AS costo_item
        FROM kardex_almacen_detalle AS kad JOIN kardex_almacen AS k ON kad.id_transaccion=k.id_transaccion LEFT JOIN almacen AS alm ON kad.id_almacen_entrada=alm.cod_almacen LEFT JOIN item AS ite ON kad.id_item=ite.id_item WHERE kad.id_transaccion = " . $_GET["id_transaccion"]);
            } else if ($_GET["id_tipo_movimiento_almacen"] == '4' || $_GET["id_tipo_movimiento_almacen"] == '2' || $_GET["id_tipo_movimiento_almacen"] == '8') {
                $operacion = "Salida";
                $campos = $conn->ObtenerFilasBySqlSelect("SELECT *,kad.cantidad as cantidad_item,kad.precio AS costo_item
        from kardex_almacen_detalle as kad join kardex_almacen as k on kad.id_transaccion=k.id_transaccion left join almacen as alm on kad.id_almacen_salida=alm.cod_almacen left join item as ite on kad.id_item=ite.id_item WHERE kad.id_transaccion = " . $_GET["id_transaccion"]);
            } else if ($_GET["id_tipo_movimiento_almacen"] == '5') {
                $operacion = "Traslado";
                $campos = $conn->ObtenerFilasBySqlSelect("SELECT *,kad.cantidad as cantidad_item,kad.precio AS costo_item
        from kardex_almacen_detalle as kad left join kardex_almacen as k on kad.id_transaccion=k.id_transaccion left join almacen as alm on kad.id_almacen_entrada=alm.cod_almacen left join item as ite on kad.id_item=ite.id_item WHERE kad.id_transaccion = " . $_GET["id_transaccion"]);
                $campos1 = $conn->ObtenerFilasBySqlSelect("SELECT *,kad.cantidad as cantidad_item,kad.precio AS costo_item
        from kardex_almacen_detalle as kad join kardex_almacen as k on kad.id_transaccion=k.id_transaccion left join almacen as alm on kad.id_almacen_salida=alm.cod_almacen left join item as ite on kad.id_item=ite.id_item WHERE kad.id_transaccion = " . $_GET["id_transaccion"]);
            }
//$campos = $conn->ObtenerFilasBySqlSelect("SELECT *,kad.cantidad as cantidad_item
//from kardex_almacen_detalle as kad left join almacen as alm on kad.id_almacen_entrada=alm.cod_almacen left join item as ite on kad.id_item=ite.id_item WHERE id_transaccion = ".$_GET["id_transaccion"]);
//echo $campos;
            if (count($campos) == 0) {
                exit;
            }

            if ($_GET["id_tipo_movimiento_almacen"] == '5') {
                echo '<tr class="detalle_items">
          <td colspan="8">
            <div style=" background-color:#f3ed8b; border-radius: 7px; padding:1px; margin-top:0.3%; margin-bottom: 10px;padding-bottom: 7px;margin-left: 10px; font-size: 13px;">
                <table >
                    <thead>
                        <th style="width:110px; font-weight: bold; text-align: center;">ID</th>
                        <th style="width:150px; font-weight: bold;">Almac&eacute;n Entrada</th>
                        <th style="width:150px; font-weight: bold;">Almac&eacute;n Salida</th>
                        <th style="width:300px; font-weight: bold;">Item</th>
                        <th style="width:110px; font-weight: bold; text-align: center;">Cantidad</th>
                        <th style="width:110px; font-weight: bold; text-align: center;">Costos</th>
                    </thead>
                    <tbody>';
            } else {
                echo '<tr class="detalle_items">
          <td colspan="8">
            <div style=" background-color:#f3ed8b; border-radius: 7px; padding:1px; margin-top:0.3%; margin-bottom: 10px; padding-bottom: 7px;margin-left: 10px; font-size: 13px;">
                <table >
                    <thead>
                        <th style="width:110px; font-weight: bold; text-align: center;">Referencia</th>
                        <th style="width:150px; font-weight: bold;">Almac&eacute;n ' . $operacion . '</th>
                        <th style="width:300px; font-weight: bold;">Item</th>
                        <th style="width:110px; font-weight: bold; text-align: center;">Cantidad</th>
                        <th style="width:110px; font-weight: bold; text-align: center;">Costos</th>
                    </thead>
                    <tbody>';
            }

            foreach ($campos as $key => $item) {
                if ($_GET["id_tipo_movimiento_almacen"] == '5') {
                    echo '
                        <tr>
                            <td style="width:110px; text-align: right; padding-right:10px;">' . $item["id_transaccion_detalle"] . '</td>
                            <td style="width:150px; padding-left:10px;">' . $item["descripcion"] . '</td>
                            <td style="width:150px;">' . $campos1[0]["descripcion"] . '</td>
                            <td style="width:300px;">' . $item["descripcion1"] . '</td>
                            <td style="text-align: right; padding-right:10px;">' . $item['cantidad_item'] . '</td>
                            <td style="text-align: right; padding-right:10px;">' . $item['costo_item'] . '</td>                                
                        </tr>';
                } else {
                    echo '
                        <tr>
                            <td style="width:110px; text-align: right; padding-right:10px;">' . $item["referencia"] . '</td>
                            <td style="width:150px; padding-left:10px;">' . $item["descripcion"] . '</td>
                            <td style="width:300px; padding-left:10px;">' . $item["descripcion1"] . '</td>
                            <td style="text-align: right; padding-right:10px;">' . $item['cantidad_item'] . '</td>
                            <td style="text-align: right; padding-right:10px;">' . $item['costo_item'] . '</td>
                        </tr>';
                }
            }

            if ($campos[0]["estado"] == "Pendiente") {
                echo '<tr>
                            <td colspan="6" style="text-align: left; border-bottom: 1px solid #949494;width:110px;">
<br/><!--form>
<label for="fecha">Fecha</label><input type="text" name="fecha">
<label for="control">Nro. Control</label><input type="text" name="control">
<label for="factura">Nro. Factura</label><input type="text" name="factura"-->
<table style="cursor: pointer;" align="right" class="btn_bg" onClick="javascript:window.location=\'?opt_menu=3&opt_seccion=109&opt_subseccion=add&cod=' . $_GET["id_transaccion"] . '&cod2=' . $_GET["cod_edocuenta"] . '\'" name="buscar" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0px;" align="right"><img src="../../libs/imagenes/bt_left.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                    <td class="btn_bg"><img src="../../libs/imagenes/factu.png" width="16" height="16" /></td>
                                    <td class="btn_bg" nowrap style="padding: 0px 1px;">Realizar Entrada</td>
                                    <td style="padding: 0px;" align="left"><img  src="../../libs/imagenes/bt_right.gif" alt="" width="4" height="21" style="border-width: 0px;" /></td>
                                </tr>
                            </table>
                            <!--/form-->
                        </td>
                        </tr>';
            }
            echo
            '</tbody>
    </table>
    </div>
    </td>
    </tr>';

            break;

        case "det_items_entrada":
            
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM kardex_almacen_detalle where id_entrada = '" . $_GET["id_transaccion"] . "'");

            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
               echo json_encode($campos);
            }
         break;
         
        case "getAlmacen":
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM almacen");

            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
               echo json_encode($campos);
            }

        case "getAlmacenStock":
            header("Content-Type: text/plain");
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM almacen");

            if (count($campos) == 0) {
                echo "[{id:'-1'}]";
            } else {
                    echo json_encode(array(
                        "success" => true,
                        "data" => $campos
                    ));
            }        
            break;
            
        case 'listaCXPpendientes':
            header("Content-Type: text/plain");
            $groupByBeneficiario = isset($_POST["groupBene"]) ? 'si' : 'no';
            if ($groupByBeneficiario == "no") {
                $sql_ = "SELECT   pro.id_proveedor, cxpd. * , pro.descripcion AS beneficiario, pro.rif, cxpd.monto as monto_pagar, (
    SELECT ifnull( sum( monto ) , 0.00 )
    FROM cxp_edocuenta_detalle
    WHERE cod_edocuenta = cxp.cod_edocuenta
    AND tipo = 'd'
    ) AS sum_debito, (

    SELECT ifnull( sum( monto ) , 0.00 )
    FROM cxp_edocuenta_detalle
    WHERE cod_edocuenta = cxp.cod_edocuenta
    AND tipo = 'c'
    ) AS sum_credito, (
    (

    SELECT ifnull( sum( monto ) , 0.00 )
    FROM cxp_edocuenta_detalle
    WHERE cod_edocuenta = cxp.cod_edocuenta
    AND tipo = 'c'
    ) - (
    SELECT ifnull( sum( monto ) , 0.00 )
    FROM cxp_edocuenta_detalle
    WHERE cod_edocuenta = cxp.cod_edocuenta
    AND tipo = 'd' )
    ) AS monto_pendiente
    FROM cxp_edocuenta_detalle cxpd
    INNER JOIN cxp_edocuenta cxp ON cxpd.cod_edocuenta = cxp.cod_edocuenta
    INNER JOIN proveedores pro ON pro.id_proveedor = cxp.id_proveedor
    WHERE cxpd.marca = 'P' and cxpd.documento<>'PAGOxCOM'
    ";

                if (isset($_POST["id_proveedor"])) {
                    $sql_ .= " and  pro.id_proveedor = " . $_POST["id_proveedor"];
                }


                $campos = $conn->ObtenerFilasBySqlSelect($sql_);



                $start = isset($_POST['start']) ? $_POST['start'] : 0; //posiciÃ³n a iniciar
                $limit = isset($_POST['limit']) ? $_POST['limit'] : 30; //nÃºmero de registros a mostrar

                echo json_encode(array(
                    "success" => true,
                    "total" => count($campos),
                    "data" => array_splice($campos, $start, $limit)
                ));
            }

            if ($groupByBeneficiario == "si") {
                $sql_ = "SELECT  distinct  pro.id_proveedor, pro.descripcion AS beneficiario
FROM cxp_edocuenta_detalle cxpd
INNER JOIN cxp_edocuenta cxp ON cxpd.cod_edocuenta = cxp.cod_edocuenta
INNER JOIN proveedores pro ON pro.id_proveedor = cxp.id_proveedor
WHERE cxpd.marca = 'P' and cxpd.documento<>'PAGOxCOM'";


                $campos = $conn->ObtenerFilasBySqlSelect($sql_);

                echo json_encode(array(
                    "success" => true,
                    "total" => count($campos),
                    "data" => $campos
                ));
            }





            break;
        case "convertiraLetras":

            header("Content-Type: text/plain");

            $n = new numerosALetras();
            $numero = $_GET["monto"];
            $num_letras = $n->convertir($numero);

            $array = array(
                "success" => true,
                "monto" => $num_letras
            );
            echo json_encode($array);
            break;
        case "tesodetasientos":
            header("Content-Type: text/plain");
            $cod_cheque = $_POST["cod_cheque"];
            $sql_ = "
                SELECT cod_cheque_bauchedet, cod_cheque, descripcion, cuenta_contable,
                CASE tipo WHEN  'd' THEN monto ELSE  '' END AS debito,
                CASE tipo WHEN  'c' THEN monto ELSE  '' END AS credito
                FROM `cheque_bache_det` WHERE cod_cheque = {$cod_cheque} ORDER BY tipo DESC;";
            /* SELECT
              cod_cheque_bauchedet,
              cod_cheque,
              descripcion,
              cuenta_contable,
              case tipo when 'd' then monto else '' end as debito,
              case tipo when 'c' then monto else '' end as credito
              FROM `cheque_bache_det` WHERE cod_cheque = " . $cod_cheque . " order by tipo desc
              "; */
            $campos = $conn->ObtenerFilasBySqlSelect($sql_);
            echo json_encode(array(
                "success" => true,
                "total" => count($campos),
                "data" => $campos
            ));
            break;
        case "store_cuContable":
            header("Content-Type: text/plain");
// CONSULTA DE CUENTAS CONTABLES
            $global = new bd(SELECTRA_CONF_PYME);

            if (isset($_POST["query"])) {
                /* if ($_POST["query"] == "") {
                  $cuentalike = " order by cuenta";
                  } else {
                  $cuentalike = " and upper(concat(cuenta,' .-',Descrip)) like upper('%" . $_POST["query"] . "%') order by cuenta";
                  } */
                $cuentalike = ($_POST["query"] == "") ? " ORDER BY cuenta" : " AND UPPER (CONCAT(cuenta,' .-',Descrip)) LIKE UPPER('%{$_POST["query"]}%') ORDER BY cuenta";
            }
            $sentencia = "SELECT * FROM nomempresa WHERE bd = '{$_SESSION['EmpresaFacturacion']}';";
            $contabilidad = $global->query($sentencia);
            $fila = $contabilidad->fetch_assoc();
            $campos_cuentas_cont = $conn->ObtenerFilasBySqlSelect("SELECT CONCAT(cuenta,' .-',Descrip) AS descripcion, cuenta FROM {$fila['bd_contabilidad']}.cwconcue WHERE Tipo = 'P'" . $cuentalike);
//echo "select cuenta as descripcion, cuenta from ".$fila['bd_contabilidad'].".cwconcue WHERE Tipo='P'".$cuentalike." order Cuenta";
            $campos_cuentas_cant = $conn->ObtenerFilasBySqlSelect("SELECT cuenta AS descripcion, cuenta FROM {$fila['bd_contabilidad']}.cwconcue WHERE Tipo = 'P'" . $cuentalike);

            echo json_encode(array(
                "success" => true,
                "total" => count($campos_cuentas_cant),
                "data" => $campos_cuentas_cont
            ));
            break;
        case "store_vendedores":
            $campos_comunes = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vendedor ORDER BY nombre");
#$campos_comunes = $conn->ObtenerFilasBySqlSelect("SELECT * FROM vendedor WHERE nombre = '".$_GET["nombre_vendedor"]."'");
            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes),
                "data" => $campos_comunes
            ));
            break;
        case "store_tipoCuenta":
            $campos_comunes = $conn->ObtenerFilasBySqlSelect("SELECT * FROM tipo_cuenta_banco");
            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes),
                "data" => $campos_comunes
            ));
            break;
        case "aCheBaucheDetCRUP":

            if ($_POST["cod_cheque_bauchedet"] != "" && $_POST["in_deleted"] != 1) {//UPDATIAR
                $sql = "
            update cheque_bache_det set
                        `monto` = " . $_POST["monto"] . ",
                        `tipo` = '" . (($_POST["tipo_a"] == "Debito") ? 'd' : 'c') . "',
                        `descripcion` = '" . $_POST["descripcion"] . "',
                        cuenta_contable = '" . $_POST["cuenta_contable"] . "'
           WHERE cod_cheque_bauchedet = " . $_POST["cod_cheque_bauchedet"];
                $conn->Execute2($sql);
            } elseif ($_POST["in_deleted"] == "1") {

                $sql = "delete from cheque_bache_det WHERE cod_cheque_bauchedet = " . $_POST["cod_cheque_bauchedet"];
                $conn->Execute2($sql);
            } else {//NUEVO ASIENTO CHEQUE BAUCHE DET
                $sql = "
            INSERT INTO `cheque_bache_det` (
                        `cod_cheque`,
                        `monto`,
                        `tipo`,
                        `fecha`,
                        `descripcion`,
                        `fecha_creacion`,
                        `usuario_creacion`,cuenta_contable)
                        VALUES (
                            " . $_POST["cod_cheque"] . ",
                            " . $_POST["monto"] . ",
                            '" . (($_POST["tipo_a"] == "Debito") ? 'd' : 'c') . "',
                            '" . date("Y-m-d") . "',
                            '" . $_POST["descripcion"] . "',
                            CURRENT_TIMESTAMP,
                            '" . $_SESSION['usuario'] . "',
                            '" . $_POST["cuenta_contable"] . "');";
                $conn->Execute2($sql);
            }

            echo json_encode(array(
                "success" => true,
                "msg" => "Asiento registrado exitosamente."
            ));


            break;
        case "listaProveedores":
            $campos_comunes = $conn->ObtenerFilasBySqlSelect("
    select
        id_proveedor,
        cod_proveedor,
        descripcion as  beneficiario,
        direccion,
        telefonos,
        fax,
        email,
        rif,
        nit
    from
        proveedores
	WHERE
	estatus='A'");
            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes),
                "data" => $campos_comunes
            ));
            break;
        case 'movimientos_bancarios_conciliar':

            list($dia1, $mes1, $anio1) = explode("/", $_POST["fecha1_"]);
            list($dia2, $mes2, $anio2) = explode("/", $_POST["fecha2_"]);
            $fecha1 = $anio1 . "-" . $mes1 . "-" . $dia1;
            $fecha2 = $anio2 . "-" . $mes2 . "-" . $dia2;
            $cod_cuenta = $_POST["cod_cuenta"];
            $sql = "
SELECT
mb.cod_movimiento_ban,
mb.cod_tesor_bancodet,
tm.descripcion as tipo_movimiento_desc,
mb.numero_movimiento,
mb.fecha_movimiento,
mb.concepto,
case when mb.tipo_movimiento =  3 or mb.tipo_movimiento =  4 then mb.monto  else 0 end debe,
case when mb.tipo_movimiento  =  1 or mb.tipo_movimiento =  2 then mb.monto  else 0 end haber,
mb.tipo_movimiento,
mb.estado,
mb.cod_conciliacion,
'false' as conciliar
 FROM `movimientos_bancarios` mb inner join tipo_movimientos_ban tm
 on tm.cod_tipo_movimientos_ban = mb.tipo_movimiento
 WHERE mb.fecha_movimiento between '" . $fecha1 . "' and '" . $fecha2 . "'
 and mb.cod_tesor_bancodet = " . $cod_cuenta . "  and mb.cod_conciliacion is null
order by mb.cod_movimiento_ban";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes),
                "data" => $campos_comunes
            ));

            break;

        case 'cxpIvaFactura':

            $MONTOBASE = $_GET[montoBase];
            $codIva = $_GET[codIva];

            $ivas = $conn->ObtenerFilasBySqlSelect("select li.alicuota, fi.formula from lista_impuestos li join formulacion_impuestos fi on (li.cod_formula=fi.cod_formula) WHERE li.cod_impuesto=$codIva");
            $PORCENTAJE = $ivas[0][alicuota];
            eval($ivas[0][formula]);
            echo $cad = $PORCENTAJE . '-' . $MONTO;
            break;

        case 'cxpRetIslrFactura':
            $par1 = $conn->ObtenerFilasBySqlSelect("select unidad_tributaria from parametros_generales");
            $id_item = $_GET[servicio];
            $cod_entidad = $_GET[entidad];
            $item_totalsiniva = $_GET[montoBase];
            $islr = $conn->ObtenerFilasBySqlSelect("select si.cod_lista_impuesto, fi.formula, li.alicuota, li.pago_mayor_a, li.monto_sustraccion, li.descripcion, li.cod_impuesto from servicios_islr si join lista_impuestos li on (si.cod_lista_impuesto=li.cod_impuesto) join formulacion_impuestos fi on (fi.cod_formula=li.cod_formula) WHERE si.cod_item=$id_item and li.cod_entidad=$cod_entidad and li.pago_mayor_a<$item_totalsiniva");
            if ($islr[0]) {
                $UT = $par1[0]["unidad_tributaria"];
                $FACTORSUST = $islr[0]["monto_sustraccion"];
                $FACTORM = $islr[0]["pago_mayor_a"];
                $PORCENTAJE = $islr[0]["alicuota"];
                $MONTOBASE = $item_totalsiniva;
                $formula = $islr[0]["formula"];
                eval($formula);

                echo number_format($MONTO, 2, ".", "");
            }
            else
                echo $cad = 0;
            break;

        case 'retencionesFactura':

            $codFacs = $_GET["facs"];

            $retenciones = $conn->ObtenerFilasBySqlSelect("SELECT cpf.cod_impuesto, li.descripcion, sum(cpf.monto_iva) as base, porcentaje_iva_retenido, sum(cpf.monto_retenido) as montoRet, sum(cpf.monto_exento) as exento, li.cod_tipo_impuesto FROM cxp_factura cpf JOIN lista_impuestos li ON ( li.cod_impuesto = cpf.cod_impuesto ) WHERE id_factura in ($codFacs) group by cpf.cod_impuesto");
            $reg = '';
            $i = 0;
            foreach ($retenciones as $key => $campos) {
                if ($campos[montoRet] > 0) {
                    $reg.="<tr><TD><input type='hidden' name='codImp$i' id='codImp$i' value='$campos[cod_impuesto]'><input type='hidden' name='exento$i' id='exento$i' value='$campos[exento]'><input type='hidden' name='tipoImp$i' id='tipoImp$i' value='$campos[cod_tipo_impuesto]'>$campos[descripcion]</TD><TD> <input type='text' style='border:0px; background-color:#ffffff;' size='15' name='base$i' id='base$i' value='$campos[base]'></TD> <TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='porcen$i' id='porcen$i' value='$campos[porcentaje_iva_retenido]'></TD><TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='montoRet$i' id='montoRet$i' value='$campos[montoRet]'></TD></tr>";
                    $i++;
                }
            }

            $retenciones2 = $conn->ObtenerFilasBySqlSelect("SELECT cpfd.cod_impuesto, li.descripcion, sum(cpfd.monto_base) as base, porcentaje_retenido, sum(cpfd.monto_retenido) as montoRet, li.cod_tipo_impuesto FROM cxp_factura_detalle cpfd JOIN lista_impuestos li ON ( li.cod_impuesto = cpfd.cod_impuesto ) WHERE id_factura_fk in ($codFacs) group by cpfd.cod_impuesto");
            foreach ($retenciones2 as $key => $campos) {
                $reg.="<tr><TD><input type='hidden' name='codImp$i' id='codImp$i' value='$campos[cod_impuesto]'><input type='hidden' name='exento$i' id='exento$i' value='$campos[exento]'><input type='hidden' name='tipoImp$i' id='tipoImp$i' value='$campos[cod_tipo_impuesto]'>$campos[descripcion]</TD><TD> <input type='text' style='border:0px; background-color:#ffffff;' size='15' name='base$i' id='base$i' value='$campos[base]'></TD> <TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='porcen$i' id='porcen$i' value='$campos[porcentaje_retenido]'></TD><TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='montoRet$i' id='montoRet$i' value='$campos[montoRet]'></TD></tr>";
                $i++;
            }
            $reg.="*l*l*l*" . $i;
            echo $reg;
            break;

// 	case 'retencionesFactura':
//
// 		$codFacs=$_GET["facs"];
// 		$retenciones2=$conn->ObtenerFilasBySqlSelect("SELECT cpfd.cod_impuesto, li.descripcion, sum(cpfd.monto_base) as base, porcentaje_retenido, sum(cpfd.monto_retenido) as montoRet, li.cod_tipo_impuesto FROM cxp_factura_detalle cpfd JOIN lista_impuestos li ON ( li.cod_impuesto = cpfd.cod_impuesto ) WHERE id_factura_fk in ($codFacs) group by cpfd.cod_impuesto");
// 		foreach($retenciones2 as $key => $campos)
// 		{
// 			$reg.="<tr><TD><input type='hidden' name='codImp$i' id='codImp$i' value='$campos[cod_impuesto]'><input type='hidden' name='exento$i' id='exento$i' value='$campos[exento]'><input type='hidden' name='tipoImp$i' id='tipoImp$i' value='$campos[cod_tipo_impuesto]'>$campos[descripcion]</TD><TD> <input type='text' style='border:0px; background-color:#ffffff;' size='15' name='base$i' id='base$i' value='$campos[base]'></TD> <TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='porcen$i' id='porcen$i' value='$campos[porcentaje_retenido]'></TD><TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='montoRet$i' id='montoRet$i' value='$campos[montoRet]'></TD></tr>";
// 			$i++;
// 		}
// 		$reg.="*l*l*l*".$i;
// 		echo $reg;
// 	break;

        case 'anticipos':

            $edoCta = $_GET["edoCta"];
            $retenciones2 = $conn->ObtenerFilasBySqlSelect("SELECT * FROM cxp_edocuenta_detalle WHERE cod_edocuenta=$edoCta AND tipo='d' and cod_edocuenta_detalle not in (select cxp_edocuenta_detalle_fk from cxp_factura_pago) and marca in ('P','X')");
            $reg = '';
            $i = 0;
            foreach ($retenciones2 as $key => $campos) {
                $reg.="<tr><TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='numero$i' id='numero$i' value='$campos[numero]'></TD><TD>$campos[descripcion]</TD><TD> <input type='text' style='border:0px; background-color:#ffffff;' size='15' name='monto$i' id='monto$i' value='$campos[monto]'></TD><TD><input name='optAnticipo{$i}' id='optAnticipo{$i}' type='checkbox' onchange='javascript:totalAnticipos();' value='{$i}'></TD></tr>";
                $i++;
            }
            $reg.="*l*l*l*" . $i;
            echo $reg;
            break;

        case 'cambiarClave';
            $clave = $_GET["clave1"];
            $clave2 = $_GET["claveOLD"];

            $usuario = $login->getIdUsuario();
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT * FROM usuarios WHERE cod_usuario = '" . $login->getIdUsuario() . "' and
		 clave='" . $_GET["claveOLD"] . "'");

//echo "SELECT * FROM usuarios WHERE cod_usuario = '".$login->getIdUsuario()."' and
// clave='".$_GET["claveOLD"]."'";
//count($campos);
            if (count($campos) == 0) {
                echo "1";
            } else {
                /* echo "update usuarios set
                  `clave` = '".$_GET["clave1"]."'
                  WHERE cod_usuario = ".$login->getIdUsuario();
                 */
                $sql = "UPDATE usuarios SET `clave` = '" . $_GET["clave1"] . "' WHERE cod_usuario ='{$usuario}'";
                $conn->Execute2($sql);
//echo "-1";
            }
            break;
        case 'anularFactura';
            $idFac = $_GET["idFac"];
            $sql = "UPDATE cxp_factura SET cod_estatus = 2 WHERE id_factura = {$idFac};";
            $conn->Execute2($sql);
            break;
        case "eliminar_ordenCXP":
            $instruccion = "UPDATE cxp_edocuenta SET marca='A', fecha_anulacion='{$_GET["fechaOrden"]}', observacion_anulado='{$_GET ["motivoAnulacionOrden"]}' WHERE cod_edocuenta = '{$_GET["cod"]}'";
            $conn->Execute2($instruccion);
            /*
             * Modificado por: Charli Vivenes
             * Objetivo: incluir la eliminacion de las entradas en el inventario cuando se cancela la compra.
             * Desccripcion: se creo la tabla 'compra_kardex' para mantener la relacion entre el kardex y la compra
             *
             */
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT id_kardex FROM compra_kardex WHERE id_compra = {$_GET["cod"]};");
            $campos2 = $conn->ObtenerFilasBySqlSelect("SELECT estado FROM kardex_almacen WHERE id_transaccion = {$campos[0]["id_kardex"]};");
            $campos3 = $conn->ObtenerFilasBySqlSelect("SELECT * FROM kardex_almacen_detalle WHERE id_transaccion = {$campos[0]["id_kardex"]};");
            $instruccion = "INSERT INTO kardex_almacen (tipo_movimiento_almacen, autorizado_por, observacion, fecha, usuario_creacion, fecha_creacion, estado, fecha_ejecucion)
                VALUES (8, 'Nadie', 'Salida por Devolucion Compra', '{$_GET["fechaOrden"]}', '{$_SESSION["usuario"]}', CURRENT_TIMESTAMP, 'Entregado', CURRENT_TIMESTAMP);";
            $conn->ExecuteTrans($instruccion);
            $id_kardex_almacen = $conn->getInsertID();
            /*
             * En este punto decidi registrar el detalle de la devolución.
             * Por ello está comentada la condición que fue relegada al interior del foreach.
             */
#if ($campos2[0]["estado"] == "Entregado") {
            foreach ($campos3 as $key => $kardex_almacen_detalle) {
                $conn->ExecuteTrans("INSERT INTO kardex_almacen_detalle (id_transaccion_detalle, id_transaccion, id_almacen_entrada, id_almacen_salida, id_item, cantidad)
                    VALUES (NULL, '{$id_kardex_almacen}', '0', '{$kardex_almacen_detalle["id_almacen_entrada"]}', '{$kardex_almacen_detalle["id_item"]}', '{$kardex_almacen_detalle["cantidad"]}');");
                if ($campos2[0]["estado"] == "Entregado") {
                    $conn->ExecuteTrans("UPDATE item_existencia_almacen SET cantidad = cantidad - '{$kardex_almacen_detalle["cantidad"]}'
                    WHERE id_item  = '{$kardex_almacen_detalle["id_item"]}' AND cod_almacen = '{$kardex_almacen_detalle["id_almacen_entrada"]}';");
                }
            }
#}
            if ($campos2[0]["estado"] == "Pendiente") {
                $conn->ExecuteTrans("UPDATE kardex_almacen SET estado = 'Cancelado' WHERE id_transaccion = {$campos[0]["id_kardex"]};");
            }
            break;
        case 'movimiento':
            $cliente = $_GET["cliente"];
//$movimiento=$conn->ObtenerFilasBySqlSelect("SELECT * FROM movimientos_bancarios  WHERE id_cliente=$cliente AND monto<>monto_aplicado");
            $movimiento = $conn->ObtenerFilasBySqlSelect("SELECT cod_movimiento_ban, numero_movimiento, concepto, (monto-(ifnull(monto_aplicado,0.00))) as monto, fecha_movimiento FROM movimientos_bancarios  WHERE id_cliente=$cliente and estatus IS NULL");
            $reg = '';
            $i = 0;
            if ($movimiento) {
                foreach ($movimiento as $key => $campos) {
                    $reg.="<tr><TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='numerom$i' id='numerom$i' value='$campos[numero_movimiento]'><input type='hidden' name='codmov$i' id='codmov$i' value='$campos[cod_movimiento_ban]'></TD><TD>$campos[concepto]</TD> <TD><input type='text' style='border:0px; background-color:#ffffff;' size='15' name='fechamov$i' id='fechamov$i' value='" . fecha($campos[fecha_movimiento]) . "'></TD><TD> <input type='text' style='border:0px; background-color:#ffffff;' size='15' name='montosss$i' id='montosss$i' value='$campos[monto]'></TD><TD><input name='optMov{$i}' id='optMov{$i}' type='checkbox' onchange='javascript:totalPagos();' value='{$i}'></TD></tr>";
                    $i++;
                }
            }
            $reg.="*l*l*l*" . $i;
            echo $reg;
            break;
        case "filtroItemByCodigoBarra":
            $tipo_item = (isset($_POST["cmb_tipo_item"])) ? $_POST["cmb_tipo_item"] : "1,2";

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;
            $cod_almacen = (isset($_POST["cod_almacen"])) ? $_POST["cod_almacen"] : "";

            $codigo_barras = (string)((isset($_POST["codigoProducto"])) ? $_POST["codigoProducto"] : "");

            if($cod_almacen){
                $filtro_por_almacen1 = sprintf("inner join item_existencia_almacen ealmacen on 
                ealmacen.id_item = i.id_item");

                $filtro_por_almacen2 = sprintf(" and ealmacen.cod_almacen = %s ",  $cod_almacen);
            } else {
                $filtro_por_almacen1 = "";
                $filtro_por_almacen2 = "";
            }

            $sql = "
            SELECT 
                i.*, 
                (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color,
                (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
            FROM 
                item i ".$filtro_por_almacen1."
            WHERE 
                (i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and i.codigo_barras = '{$codigo_barras}') OR
                (i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and i.id_item IN (
                    select id_item from item_cod_barra where codigo_barras = '{$codigo_barras}'
                    )) ";

            $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);

            $sql = "SELECT 
                        i.*, 
                        (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color,
                        (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
                    FROM 
                        item i ".$filtro_por_almacen1."
                    WHERE 
                        (i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and i.codigo_barras = '{$codigo_barras}') OR
                        (i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and i.id_item IN (
                            select id_item from item_cod_barra where codigo_barras = '{$codigo_barras}'
                        )) 
                    LIMIT $start,$limit";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
        
            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes1),
                "data" => $campos_comunes
            ));


            break;
        case "filtroItemByRCCB":
            /**
             * Procedimiento de busqueda de productos/servicios
             *
             * Realizado por:
             * Luis E. Viera Fernandez
             *
             * Correo:
             *      levieraf@gmail.com
             *
             */

            $tipo_item = (isset($_POST["cmb_tipo_item"])) ? $_POST["cmb_tipo_item"] : "1,2";

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;
            $cod_almacen = (isset($_POST["cod_almacen"])) ? $_POST["cod_almacen"] : "";

            if ($busqueda) {
                //filtro para productos
                // if ($tipo_item == 1) {
                    $codigo = (string)((isset($_POST["codigoProducto"])) ? $_POST["codigoProducto"] : "");
                    $andWHERE = "UPPER(i.referencia) = UPPER('".$codigo."') or UPPER(i.cod_item) like UPPER('%".$codigo."%') OR (select count(*) from item_cod_barra A where A.codigo_barras  = UPPER('".$codigo."') and A.id_item = i.id_item ) > 0 or UPPER(i.codigo_barras) like UPPER('%".$codigo."%')";
                // }

                if($cod_almacen){
                    $filtro_por_almacen1 = sprintf("inner join item_existencia_almacen ealmacen on 
                    ealmacen.id_item = i.id_item");

                    $filtro_por_almacen2 = sprintf(" and ealmacen.cod_almacen = %s ",  $cod_almacen);
                } else {
                    $filtro_por_almacen1 = "";
                    $filtro_por_almacen2 = "";
                }

                $sql = "
                SELECT i.*, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color 
                FROM item i ".$filtro_por_almacen1."
                WHERE i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and (" . $andWHERE . ") ";
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);

                $sql = "SELECT 
                            i.*, 
                            (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM nncolor where item = i.id_item) as posee_talla_color,
                            (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
                        FROM 
                            item i ".$filtro_por_almacen1."

                WHERE i.cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2." and " . $andWHERE . " 

                    




                limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            } else {
                $sql = "SELECT i.*, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color FROM item i WHERE i.cod_item_forma = " . $tipo_item;
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "SELECT 
                            i.*, 
                            (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color,
                            (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
                        FROM 
                            item i 
                        WHERE i.cod_item_forma = " . $tipo_item . " 
                        LIMIT $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }
            
            echo json_encode(array(
                "success" => true,
                "total" => count($campos_comunes1),
                "data" => $campos_comunes
            ));

            break;

        case "filtroItem":
            /**
             * Procedimiento de busqueda de productos/servicios
             *
             * Realizado por:
             * Luis E. Viera Fernandez
             *
             * Correo:
             *      levieraf@gmail.com
             */

            $tipo_item = (isset($_POST["cmb_tipo_item"])) ? $_POST["cmb_tipo_item"] : "1,2";

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;
            $cod_almacen = (isset($_POST["cod_almacen"])) ? $_POST["cod_almacen"] : "";

            $cod_departamento = (isset($_POST["cod_departamento"])) ? $_POST["cod_departamento"] : "";
            $cod_grupo = (isset($_POST["cod_grupo"])) ? $_POST["cod_grupo"] : "";
            $cod_linea = (isset($_POST["cod_linea"])) ? $_POST["cod_linea"] : "";

            if ($busqueda) {
                //filtro para productos
                    $codigo = (isset($_POST["codigoProducto"])) ? $_POST["codigoProducto"] : "";
                    $codigo_barras = (isset($_POST["codigoBarrasProducto"])) ? $_POST["codigoBarrasProducto"] : "";
                    $descripcion = (!isset($_POST["descripcionProducto"])) ? "" : $_POST["descripcionProducto"];
                    $referencia = (!isset($_POST["referencia"])) ? "" : $_POST["referencia"];

                    $andWHERE = " and ";
                    if ($codigo != "") {
                        $andWHERE .= " ( cod_item like '%" . $codigo . "%' or id_item  = '".$codigo."') ";
                        $entrada_codigo=true;
                    }

################################################################################
                    if ($codigo_barras != "") {

                        if ($codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " upper(codigo_barras) like upper('%" . $codigo_barras . "%')";
                    }

                    if ($referencia != "") {
                        if ($codigo_barras != "" || $codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " upper(referencia) like upper('%" . $referencia . "%')";
                    }

################################################################################
                    if ($descripcion != "") {
                        if ($codigo_barras != "" || $referencia != "" || $codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " upper(descripcion1) like upper('%" . $descripcion . "%')";
                    }

                    /*$buscarEn = (!isset($_POST["buscarEn"])) ? "" : $_POST["buscarEn"];
                    if($buscarEn != ""){
                        $andWHERE = " and ( cod_item like '%" . $buscarEn . "%' ";

                        $andWHERE .= " or descripcion1 like '%" . $buscarEn . "%'";

                        $andWHERE .= " or codigo_barras like '%" . $buscarEn . "%'";

                        $andWHERE .= " or referencia like '%" . $buscarEn . "%')";
                    }*/

                    if ($cod_departamento != "") {
                        if ($descripcion != "" || $codigo_barras != "" || $referencia != "" || $codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " cod_departamento = " . $cod_departamento;
                    }

                   if ($cod_grupo != "") {
                        if ($cod_departamento != "" || $descripcion != "" || $codigo_barras != "" || $referencia != "" || $codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " cod_grupo = " . $cod_grupo;
                    }

                   if ($cod_linea != "") {
                        if ($cod_grupo != "" || $descripcion != "" || $codigo_barras != "" || $referencia != "" || $codigo != "" || $cod_departamento != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " cod_linea = " . $cod_linea;
                    }

                    if ($codigo == "" && $descripcion == "" && $codigo_barras == "" && $referencia == "" && $cod_departamento == "" && $cod_grupo == "" && $cod_linea == "") {
                        $andWHERE = "";
                    }

                    $buscarEn = (!isset($_POST["buscarEn"])) ? "" : $_POST["buscarEn"];
                    if($buscarEn != ""){
                        $andWHERE = " and ( cod_item  like '%" . $buscarEn . "%' ";

                        $andWHERE .= " or descripcion1 like '%" . $buscarEn . "%'";

                        $andWHERE .= " or codigo_barras like '%" . $buscarEn . "%'";

                        $andWHERE .= " or referencia like '%" . $buscarEn . "%')";
                    }

                    $seleccionado = (!isset($_POST["seleccionado"])) ? "" : $_POST["codigoProducto"];
                    if($seleccionado != ""){
                        $andWHERE = " and id_item = '" . $seleccionado . "'";
                    }

                $sql = "SELECT count(*) as cantidad
                FROM item i 
                WHERE cod_item_forma in (" . $tipo_item . ") " . $andWHERE;

                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                $sql = "
                    SELECT 
                        *,(SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 2) pa,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 3) pb,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 4) pc,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 5) pd,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 6) pe,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 7) pf,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 8) ph,
                           
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 2) ppa,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 3) ppb,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 4) ppc,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 5) ppd,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 6) ppe,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 7) ppf,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 8) pph,                        

                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 2) dppa,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 3) dppb,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 4) dppc,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 5) dppd,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 6) dppe,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 7) dppf,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 8) dpph
                        


, concat('A:',precio1,'<br>B:',precio2,'<br>C',precio3,'<br>D:',precio4,'<br>E:',precio5,'<br>F:',precio6) as tprecio,
                        (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color,
                        (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
                    
, (select ifnull(sum(ie.cantidad),0)   from item_existencia_almacen ie where ie.id_item = i.id_item ) as stock

                    FROM 
                        item i 
                    WHERE 
                        cod_item_forma in (" . $tipo_item . ") " . $andWHERE . " 
                    LIMIT $start,$limit";

                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            } else {

                if($cod_almacen){
                    $filtro_por_almacen1 = sprintf("left join item_existencia_almacen ealmacen on 
                    ealmacen.id_item = i.id_item");

                    $filtro_por_almacen2 = sprintf(" and ealmacen.cod_almacen = %s ",  $cod_almacen);
                    $group = " group by i.id_item";
                    $existencia_campo = ",ifnull(sum(ie.cantidad),0)  stock";
                } else {
                    $filtro_por_almacen1 = "";
                    $filtro_por_almacen2 = "";
                    $group = " group by i.id_item";
                    $existencia_campo = ",ifnull(sum(ie.cantidad),0) stock";
                }

                $sql = "SELECT count(*) as cantidad
                FROM item i ".$filtro_por_almacen1."
                WHERE cod_item_forma in (" . $tipo_item .") ". $filtro_por_almacen2;

                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);

                $seleccionado = (!isset($_POST["seleccionado"])) ? "" : $_POST["codigoProducto"];
                if($seleccionado != ""){
                    $andWHERE = " and id_item = '" . $seleccionado . "'";
                }

                $sql = "SELECT 
                            * 
                            
,(SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 2) pa,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 3) pb,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 4) pc,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 5) pd,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 6) pe,
                       (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 7) pf,
                        (SELECT cod_tipo_precio FROM tipo_precio where cod_tipo_precio = 8) ph,
                           
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 2) ppa,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 3) ppb,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 4) ppc,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 5) ppd,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 6) ppe,
                       (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 7) ppf,
                        (SELECT cod_referencia FROM tipo_precio where cod_tipo_precio = 8) pph,                        

                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 2) dppa,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 3) dppb,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 4) dppc,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 5) dppd,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 6) dppe,
                       (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 7) dppf,
                        (SELECT descripcion FROM tipo_precio where cod_tipo_precio = 8) dpph
                        


, concat('A:',precio1,'<br>B:',precio2,'<br>C',precio3,'<br>D:',precio4,'<br>E:',precio5,'<br>F:',precio6) as tprecio,

                            (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color,
                            (SELECT case when count(*) > 0 then 'si' else 'no' end FROM item_seriales items where  items.id_item = i.id_item and items.status = 1) as posee_serial
                             

".$existencia_campo."
                        FROM 
                            item i ".$filtro_por_almacen1."  join item_existencia_almacen ie on 
                    ie.id_item = i.id_item
                        WHERE 
                            cod_item_forma in (" . $tipo_item . ") ". $filtro_por_almacen2 . $andWHERE." ".$group." 
                            order by i.id_item  
                        LIMIT $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }

            echo json_encode(array(
                "success" => true,
                "total" => (count($campos_comunes1)>0) ? $campos_comunes1[0]["cantidad"] : 0,
                "data" => $campos_comunes
            ));

        
            
            
            
            
            break;

        case "saldoPendientePorProveedorModCaja":
            $id_proveedor = $_GET["id_proveedor"];
                
            /*$sql = "SELECT 
                            id,
                            fecha, 
                            comprobante, 
                            numero, 
                            saldo, 
                            monto,
                            id_proveedor
                    FROM caja_egreso_saldo_x_proveedores
                    WHERE id_proveedor =  {$id_proveedor} and saldo > 0";*/
            $sql = "select c.caja_id id, c.fecha,c.comprobante,c.comprobante_numero numero, 
                (c.monto - coalesce(sum(cd.monto),0)) saldo,'{$id_proveedor}' id_proveedor
                 from caja_nueva c 
                  left join caja_nueva_detalle cd on cd.caja_id = c.caja_id 
                  where c.status = 'Pendiente' and c.id_proveedor = '{$id_proveedor}' 
                  group by c.caja_id; ";
            
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode($campos_comunes);

            break;

   case "filtroItemGeneral":
            $tipo_item = (isset($_POST["cmb_tipo_item"])) ? $_POST["cmb_tipo_item"] : 1;

            $busqueda = (isset($_POST["BuscarBy"])) ? $_POST["BuscarBy"] : "";
            $limit = (isset($_POST["limit"])) ? $_POST["limit"] : 10;
            $start = (isset($_POST["start"])) ? $_POST["start"] : 0;

            if ($busqueda) {
                //filtro para productos
                if ($tipo_item == 1) {
                    $referencia = (isset($_POST["referencia"])) ? $_POST["referencia"] : "";
                    $codigo = (isset($_POST["codigoProducto"])) ? $_POST["codigoProducto"] : "";
                    $descripcion = (!isset($_POST["descripcionProducto"])) ? "" : $_POST["descripcionProducto"];

                    
                    if($codigo){
                            $andWHERE .= " and ( cod_item like '%" . $codigo . "%' or id_item  = '".$codigo."') ";

                           // $andWHERE .= " or upper(codigo_barras) like upper('%" . $codigo . "%')";

                           // $andWHERE .= " or upper(referencia) like upper('%" . $codigo . "%')";
                    }

                    if ($referencia != "") {
                        if ($codigo != "") {
                            $andWHERE .= " and ";
                        } else {
                            $andWHERE = " and ";
                        }
                        $andWHERE .= " upper(referencia) like upper('%" . $referencia . "%')";
                    }

                    if ($descripcion != "") {                        
                        $andWHERE .= " and upper(descripcion1) like upper('%" . $descripcion . "%')";
                    }
                    
                    if ($codigo == "" && $descripcion == "" && $referencia == "") {
                        $andWHERE = "";
                    }

                    $buscarEn = (!isset($_POST["buscarEn"])) ? "" : $_POST["buscarEn"];
                    if($buscarEn != ""){
                        $andWHERE = " and ( cod_item like '%" . $buscarEn . "%' ";

                        $andWHERE .= " or descripcion1 like '%" . $buscarEn . "%'";

                        $andWHERE .= " or codigo_barras like '%" . $buscarEn . "%'";

                        $andWHERE .= " or referencia like '%" . $buscarEn . "%')";
                    }

                    $seleccionado = (!isset($_POST["seleccionado"])) ? "" : $_POST["codigoProducto"];
                    if($seleccionado != ""){
                        $andWHERE = " and id_item = '" . $seleccionado . "'";
                    }
                    
                 }   
                //filtro para productos
                if ($tipo_item == 2) {
                    $codigo = (isset($_POST["codigoProducto"])) ? $_POST["codigoProducto"] : "";
                    $descripcion = (!isset($_POST["descripcionProducto"])) ? "" : $_POST["descripcionProducto"];

                    $andWHERE = " and ";
                    if ($codigo != "") {
                        $andWHERE .= " upper(cod_item) = upper('" . $codigo . "')";
                    }
                    if ($descripcion != "") {
                        $andWHERE .= " and upper(descripcion1) like upper('%" . $descripcion . "%')";
                    }
                    if ($codigo == "" && $descripcion == "") {
                        $andWHERE = "";
                    }
                    $buscarEn = (!isset($_POST["buscarEn"])) ? "" : $_POST["buscarEn"];
                    if($buscarEn != ""){
                        $andWHERE = " and ( cod_item = '" . $buscarEn . "' ";

                        $andWHERE .= " or descripcion1 like '%" . $buscarEn . "%'";

                        $andWHERE .= " or codigo_barras = '" . $buscarEn . "'";

                        $andWHERE .= " or referencia = '" . $buscarEn . "')";
                    }
                }                

                //$sql = "SELECT *, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color FROM item i WHERE cod_item_forma = " . $tipo_item . " " . $andWHERE . " limit $start,$limit";
                $sql = "SELECT *, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM nntalla where item = i.id_item) as posee_talla_color FROM item i WHERE cod_item_forma = " . $tipo_item . " " . $andWHERE . " limit $start,$limit";
                //echo $sql;
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
                
                $sql = "SELECT count(*) as cantidad FROM item i WHERE cod_item_forma = " . $tipo_item . " " . $andWHERE . " limit $start,$limit";
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                
            } else {
                $seleccionado = (!isset($_POST["seleccionado"])) ? "" : $_POST["codigoProducto"];
                if($seleccionado != ""){
                    $andWHERE = " and id_item = '" . $seleccionado . "'";
                }

                $sql = "SELECT count(*) as cantidad FROM item i WHERE cod_item_forma = " . $tipo_item;
                $campos_comunes1 = $conn->ObtenerFilasBySqlSelect($sql);
                
                //$sql = "SELECT *, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM item_color_talla where item = i.id_item) as posee_talla_color FROM item i WHERE cod_item_forma = " . $tipo_item . " limit $start,$limit";
                $sql = "SELECT *, (SELECT case when count(*) > 0 then 'si' else 'no' end  FROM nntalla where item = i.id_item) as posee_talla_color FROM item i WHERE cod_item_forma = " . $tipo_item . " limit $start,$limit";
                $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            }
 
            echo json_encode(array(
                "success" => true,
                "total" => (count($campos_comunes1)>0) ? $campos_comunes1[0]["cantidad"] : 0,
                "data" => $campos_comunes
            ));

            break;

        case "saldoPendientePorProveedorModCaja":
            $id_proveedor = $_GET["id_proveedor"];
                
            $sql = "SELECT 
                            id,
                            fecha, 
                            comprobante, 
                            numero, 
                            saldo, 
                            monto,
                            id_proveedor
                    FROM caja_egreso_saldo_x_proveedores
                    WHERE id_proveedor =  {$id_proveedor} and saldo > 0";
            
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode($campos_comunes);

            break;
        
        
        case "saldoPendientePorClienteModCaja":
            $id_cliente = $_GET["id_cliente"];
            
            //comentado query para que se busquen las facturas pendientes en la nueva caja

            /*
            $sql = "SELECT 
                            `id_caja_ing_cob_sal_x_cli` as id,
                            `fecha`, 
                            `comprobante`, 
                            `numero`, 
                            `saldo`, 
                            `id_cliente`
                    FROM caja_ing_cob_sal_x_cli 
                    WHERE id_cliente =  {$id_cliente} and saldo > 0";*/

            $sql = "select c.caja_id id, c.fecha,c.comprobante,c.comprobante_numero numero, 
                (c.monto - coalesce(sum(cd.monto),0)) saldo,'{$id_cliente}' id_cliente, c.id_factura 
                 from caja_nueva c 
                  left join caja_nueva_detalle cd on cd.caja_id = c.caja_id 
                  inner join factura f on f.id_factura = c.id_factura 
                  inner join clientes cl on cl.id_cliente = f.id_cliente 
                  where c.status = 'Pendiente' and f.id_cliente = '{$id_cliente}' 
                  group by c.caja_id; ";
            
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode($campos_comunes);

            break;
        case "agregar_factura":
            /**
             * Procedimiento de registro de facturas sin generacion de inventario
             *
             * Realizado por:
             * Charli J. Vivenes Rengel
             *
             * Correo:
             *      cvivenes@asys.com.ve
             *      cjvrinf@gmail.com
             *
             */
#$compra = new Compra();
            $correlativos = new Correlativos();

#$compra->BeginTrans();
            $nro_compra = $correlativos->getUltimoCorrelativo("cod_compra", 1, "si");

            $sql = "INSERT INTO `compra` (
              `id_compra`, `cod_compra`, `id_proveedor`, `cod_vendedor`,
              `fechacompra`, `montoItemscompra`, `ivaTotalcompra`, `TotalTotalcompra`, `monto_excento`,
              `cantidad_items`, `cod_estatus`, `fecha_creacion`, `usuario_creacion`,
              `responsable`, `centrocosto`, `num_factura_compra`, `num_cont_factura`)
              VALUES (
              NULL , '" . $nro_compra . "', '" . $_GET["id_proveedor"] . "', '',
              '" . $_GET["fecha_emision"] . "', '" . $_GET["subtotal_factura"] . "', '" . $_GET["iva_factura"] . "', '" . ($_GET["iva_factura"] + $_GET["subtotal_factura"]) . "', '" . $_GET["exento_factura"] . "',
              '0', '1', CURRENT_TIMESTAMP , '" . $_GET["usuario"] . "',
              '" . $_GET["responsable"] . "', '', '" . $_GET["num_factura"] . "', '" . $_GET["num_control"] . "');";

#$compra->ExecuteTrans($sql);
            $conn->ExecuteTrans($sql);

            $sql_cxp = "INSERT INTO cxp_edocuenta (
		`cod_edocuenta`, `id_proveedor`, `documento`,
		`numero`, `monto`, `fecha_emision`,
		`observacion`, `vencimiento_fecha`, `vencimiento_persona_contacto`,
		`vencimiento_telefono`, `vencimiento_descripcion`,
		`usuario_creacion`, `fecha_creacion`, `marca`)
                VALUES (
		NULL, '" . $_GET["id_proveedor"] . "', 'FACxCOM',
		'" . $nro_compra . "', '" . ($_GET["iva_factura"] + $_GET["subtotal_factura"]) . "', '" . $_GET["fecha_emision"] . "',
		'Compra " . $nro_compra . "', '" . $_GET["fecha_vence"] . "', '',
		'', '' , '" . $_GET["usuario"] . "', '" . $_GET["fecha_emision"] . "', 'P');";

#$compra->ExecuteTrans($sql_cxp);
            $conn->ExecuteTrans($sql_cxp);
            $id_cxp = $conn->getInsertID();

            $SQL_cxp_DET = "INSERT INTO cxp_edocuenta_detalle (
		`cod_edocuenta_detalle`, `cod_edocuenta`, `documento`,
		`numero`, `descripcion`, `tipo`,
		`monto`, `usuario_creacion`, `fecha_creacion`,
		`fecha_emision_edodet`, `marca`)
                VALUES (
		NULL ,'" . $id_cxp . "','PAGOxCOM',
                '" . $nro_compra . "R','compra " . $nro_compra . "','c',
                '" . ($_GET["iva_factura"] + $_GET["subtotal_factura"]) . "','" . $_GET["usuario"] . "', CURRENT_TIMESTAMP,
		'" . $_GET["fecha_emision"] . "','P');";
# Se inserta el detalle de la cxp en este caso el asiento del DEBITO.
#$compra->ExecuteTrans($SQL_cxp_DET);
            $conn->ExecuteTrans($SQL_cxp_DET);
            $nro_compra = $correlativos->getUltimoCorrelativo("cod_compra", 1, "no");
            $conn->ExecuteTrans("UPDATE correlativos SET contador = '" . $nro_compra . "' WHERE campo LIKE 'cod_compra'");

            $cod_impuesto = $alicuota = $monto_retenido = 0;
            if ($_GET["retencion_iva"]) {
                $cod_impuesto = $_GET["cod_impuesto"];
                $alicuota = $_GET["alicuota"];
                $monto_retenido = $_GET["iva_factura"] * $alicuota / 100;
            }
#$sql_tipo_impuesto;
//responsable='+responsable+'&num_factura='++'&='+num_control+'&='+exento_factura+'&subtotal_factura='+subtotal_factura+'&='+base_factura+'&iva_factura='+iva_factura+'&='+fecha_emision+'&fecha_vence='+fecha_vence+'&id_proveedor='+id_proveedor+'&usuario='+usuario,
            $sql_cxp_factura = "INSERT INTO cxp_factura (
                    id_factura, cod_factura, cod_cont_factura, id_cxp_edocta, fecha_factura, fecha_recepcion,
                    monto_base, monto_exento, anticipo, monto_total_con_iva, monto_total_sin_iva,
                    cod_impuesto, porcentaje_iva_mayor, monto_iva, porcentaje_iva_retenido, monto_retenido,
                    total_a_pagar, cod_estatus, fecha_pago, fecha_creacion, usuario_creacion,
                    tipo, factura_afectada, libro_compras, cod_correlativo_iva, cod_correlativo_islr)
                VALUES (
                    NULL, '" . $_GET["num_factura"] . "', '" . $_GET["num_control"] . "', '" . $id_cxp . "', '" . $_GET["fecha_emision"] . "', '" . $_GET["fecha_emision"] . "',
                    '" . $_GET["base_factura"] . "', '" . $_GET["exento_factura"] . "', '0', '" . ($_GET["iva_factura"] + $_GET["subtotal_factura"]) . "', '" . $_GET["subtotal_factura"] . "',
                    '" . $cod_impuesto . "', 12, '" . $_GET["iva_factura"] . "', " . $alicuota . ", " . $monto_retenido . ",
                    '" . ($_GET["subtotal_factura"] + $_GET["iva_factura"] - $monto_retenido) . "', '1', '', CURRENT_TIMESTAMP, '" . $_GET["usuario"] . "',
                    'FAC', '" . $_GET["num_factura"] . "', '{$_GET["libro_compras"]}', '', '')";
            $conn->ExecuteTrans($sql_cxp_factura);

            $id_cxp_factura = $conn->getInsertID();
            $sql_cxp_factura_det = "INSERT INTO cxp_factura_detalle (
                    id_factura, id_factura_fk, monto_base, porcentaje_retenido, cod_impuesto, monto_retenido, id_item)
                VALUES (
                    NULL, '" . $id_cxp_factura . "', '" . $_GET["base_factura"] . "', '" . $_GET["alicuota"] . "', '', '')";
            $conn->ExecuteTrans($sql_cxp_factura_det);
            break;
            
			case "cambioPrecio":
            
            $campos = $conn->ObtenerFilasBySqlSelect("SELECT *
							from item
							WHERE id_item between $_GET[itemini] and $_GET[itemfin]");
            if (count($campos) == 0) {
                exit;
            }
            echo '
            		<table >
                    <thead>
                        <th style="border-bottom: 1px solid #949494;width:110px;">Cod</th>
                        <th style="border-bottom: 1px solid #949494;width:200px;">Descripcion</th>
                        <th style="border-bottom: 1px solid #949494;width:200px;">Precio1&nbsp;<input type="checkbox" name="precio1" id="precio1" value="1"></th>
								<th style="border-bottom: 1px solid #949494;width:200px;">Precio2&nbsp;<input type="checkbox" name="precio2" id="precio2" value="1"></th>
								<th style="border-bottom: 1px solid #949494;width:200px;">Precio3&nbsp;<input type="checkbox" name="precio3" id="precio3" value="1"></th>
								<th style="border-bottom: 1px solid #949494;width:200px;">Precio4&nbsp;<input type="checkbox" name="precio4" id="precio4" value="1"></th>
								<th style="border-bottom: 1px solid #949494;width:200px;">Precio5&nbsp;<input type="checkbox" name="precio5" id="precio5" value="1"></th>
								<th style="border-bottom: 1px solid #949494;width:200px;">Precio6&nbsp;<input type="checkbox" name="precio6" id="precio6" value="1"></th>

                    </thead>
                    <tbody>';


            $acuDebitos = 0;
            $acuCreditos = 0;
            foreach ($campos as $key => $item) {
                echo '
                        <tr>
                            <td align="center" style="border-bottom: 1px solid #949494;width:110px;">' . $item["cod_item"] . '
									<input type="hidden" id="id_item[]" name="id_item[]" value='.$item["id_item"].'>
                            </td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:200px;">' . $item["descripcion1"] . '</td>
                            <td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva1" name="coniva1[]" value=' . $item["coniva1"] . '></td>
									<td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva2" name="coniva2[]" value=' . $item["coniva2"] . '></td>
									<td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva3" name="coniva3[]" value=' . $item["coniva3"] . '></td>
									<td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva4" name="coniva4[]" value=' . $item["coniva4"] . '></td>
									<td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva5" name="coniva5[]" value=' . $item["coniva5"] . '></td>
									<td style="text-align: left; border-bottom: 1px solid #949494;width:110px;"><input style="text-align: right;" size="7" type="text" id="coniva6" name="coniva6[]" value=' . $item["coniva6"] . '></td>
                            
                        </tr>';
            }
            echo '</tbody></table>';
            break;
        case "TiposFormaPagoOtrosEgresos":
        case "TiposFormaPagoEgresoProveedores":
            $sql = "SELECT * FROM caja_forma_pago WHERE 
                            id_caja_tp_registro IN ( 2, 3 ) order by codigo asc ";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode($campos_comunes);
            break;
        case "cargarChequesDocTerceros":
            /*$sql = "SELECT * FROM caja_cheques_docum_terceros WHERE pagado=0";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);*/
            $sql = "SELECT distinct numero id, numero,
            fecha fecha_cheque,secuencia,banco,localidad,monto importe,local,pagado
             FROM caja_nueva_detalle_forma_pago WHERE pagado=0 and isnull(local)!=1";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            echo json_encode($campos_comunes);
            break;
        case "TiposFormaPagoOtrosIngresos":
        case "TiposFormaPago":
            $sql = "SELECT * FROM caja_forma_pago WHERE id_caja_tp_registro IN ( 1,3 ) order by codigo asc ";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);
            echo json_encode($campos_comunes);
            break;
        case "TiposFormaPagoIngresoCambioCheques":
            $sql = "SELECT * FROM caja_forma_pago WHERE codigo
                                    IN (4) order by codigo asc ";
            $campos_comunes = $conn->ObtenerFilasBySqlSelect($sql);

            echo json_encode($campos_comunes);
            break;
            
         case "agregarColorTalla":
         
         	
				$form=array();
		     	//echo $_GET["formulario"];
		     	$form=json_decode($_GET["formulario"],true);
         	/*$form=$_GET["formulario"];
				foreach ($form as $i => $input)
				{
					echo $input[0];
				}         	
         	 */
         	$ii=0;
         	$tallas=array();
         	foreach ($form as $i => $input)
				{
					if($input[name]=="item")
						$item=$input[value];
										
					$cad="talla".$ii;
			
					if($input[name]==$cad) 
					{
						$ii++;
						$tallas[]=$input[value];
					}
				}       	
         	
         	
         	$colores=array();
         	$ii=0;
         	$maxcolor=0;
         	foreach ($form as $i => $input)
				{
					$cad="color".$ii;
			
					if($input[name]==$cad) 
					{
						
						$colores[]=$input[value];
						$maxcolor=$ii;
						$ii++;
					}
				}
				
				function find_by_key($searched, $array){
					if(!is_array($array)) return FALSE; // We haven't passed a valid array
				 
					foreach($array as $key=>$value){
						if($value[name]==$searched) return $value[value]; // Match found, return value
					}
					return FALSE; // Nothing was found
				}
			    /* $sql = "truncate table item_color_talla_temp";
                $conn->ExecuteTrans($sql);*/
				for($i=0;$i<=$maxcolor;$i++)
				{
					for($j=0;$j<=15;$j++)
					{
						$cad="c".$i."t".$j;
						$valor=find_by_key($cad,$form);
						if($valor!="")
						{


   

							$sql = "SELECT * FROM item_color_talla_temp WHERE color='".$colores[$i]."' and item = '$item' and talla='".$tallas[$j]."'";
            			$cti = $conn->ObtenerFilasBySqlSelect($sql);
            			if($cti[0][cantidad]!="")
            			{
            				$sql = "update item_color_talla_temp set cantidad='".$valor."' WHERE color='".$colores[$i]."' and talla='".$tallas[$j]."' and item = '$item'";
            				$conn->ExecuteTrans($sql);	
            			}
            			else 
            			{
							$sql = "INSERT INTO item_color_talla_temp (item,color,talla,cantidad) VALUES ('$item', '".$colores[$i]."', '".$tallas[$j]."', '".$valor."')";
            				$conn->ExecuteTrans($sql);
            			}					
						}
					}
				}
            
            break;          
            case "getComprasPendientes":
                
                    $compras = $conn->ObtenerFilasBySqlSelect("SELECT c.id_compra, c.cod_compra, (c.TotalTotalcompra+c.ivaTotalcompra-c.monto_descuento )TotalTotalcompra ,d.Abreviatura simbolo
                        FROM compra c 
                        left join divisas d on d.id_divisa=c.id_divisa 
                        WHERE c.id_proveedor =  '" . $_GET["v1"] . "' and c.formapago!='Recepcion'");

                    $reg = '';
                    $i = 0;
                    foreach ($compras as $key => $campos) {
                        $reg.="<tr style='cursor:pointer;' ondblclick='javascript:cargarDetalleCompra($campos[id_compra]);' id=$campos[id_compra]><td>$campos[cod_compra]</TD><TD>$campos[simbolo] $campos[TotalTotalcompra]</td></tr>";
                        $i++;
                    }
                    
                    echo $reg;
                    
            break;          
            case "getComprasPendientesPorPagar":
                
                    $compras = $conn->ObtenerFilasBySqlSelect("SELECT c.id_compra, c.cod_compra, (c.TotalTotalcompra+c.ivaTotalcompra-c.monto_descuento )TotalTotalcompra ,d.Abreviatura simbolo
                        FROM compra c 
                        left join divisas d on d.id_divisa=c.id_divisa 
                        WHERE c.id_proveedor =  '" . $_GET["v1"] . "' and c.formapago!='Recepcion' and c.cod_estatus=1");

                    $reg = '';
                    $i = 0;
                    foreach ($compras as $key => $campos) {
                        $reg.="<tr style='cursor:pointer;' ondblclick='javascript:cargarDetalleCompra($campos[id_compra]);' id=$campos[id_compra]><td>$campos[cod_compra]</TD><TD>$campos[simbolo] $campos[TotalTotalcompra]</td></tr>";
                        $i++;
                    }
                    
                    echo $reg;
                    
            break;

            case "get_detalle_compra_ingreso":

                 $id = (isset($_GET["id"])) ? $_GET["id"] : '';

                 if ($id) {
                    $campos = $conn->ObtenerFilasBySqlSelect("SELECT b.id_detalle_compra,b ._item_cantidad cantidad,(b._item_preciosiniva/coalesce(t2.tasa,1)) precio , a.id_item, 1 id_almacen_entrada, a.cod_item, a.referencia, a.descripcion1, a.cantidad_bulto ,t2.tasa,
                            count(ct.id) tiene_tallas
                            FROM compra_detalle b
                            left join compra_detalle_cantidad_talla_color ct on ct.id_detalle_compra = b.id_detalle_compra
                            inner join compra c on c.id_compra = b.id_compra 
                            INNER JOIN item a ON a.id_item = b.id_item 
                            left join (SELECT MAX( id ) AS id, divisa FROM tasas_cambio GROUP BY divisa) t1 on t1.divisa = c.id_divisa 
                            left join tasas_cambio t2 on t1.id=t2.id 
                            WHERE b.id_compra = '" . $id ."' group by b.id_detalle_compra order by id_detalle_compra");

                    $campos2 = $conn->ObtenerFilasBySqlSelect("SELECT ct.*
                            FROM compra_detalle b
                            inner join compra_detalle_cantidad_talla_color ct on ct.id_detalle_compra = b.id_detalle_compra
                            WHERE b.id_compra = '" . $id ."'  order by id_detalle_compra");

                    if (count($campos) == 0) {
                        echo "";
                    } else {
                       //echo json_encode(array("detalles" => $campos,"tallas" => $campos2));
                        echo json_encode($campos);
                    }
                 }   
             break; 

            case "get_datos_compra_comprobannte":

                 $id = (isset($_GET["id"])) ? $_GET["id"] : '';

                 if ($id) {
                    $campos = $conn->ObtenerFilasBySqlSelect("SELECT coalesce(t2.tasa,1) tasa,(c.TotalTotalcompra - c.monto_descuento) total, c.ivaTotalcompra impuesto,c.fechacompra,'Fact' comprobante,c.cod_compra numero
                            FROM compra c 
                            left join (SELECT MAX( id ) AS id, divisa FROM tasas_cambio GROUP BY divisa) t1 on t1.divisa = c.id_divisa 
                            left join tasas_cambio t2 on t1.id=t2.id 
                            WHERE c.id_compra = " . $id );

                    if (count($campos) == 0) {
                        echo "";
                    } else {
                       echo json_encode($campos);
                    }
                 }   
             break; 

            case "buscarKardexPorComprobante":
                
                 $nro_comprobante = (isset($_GET["comprobante"])) ? $_GET["comprobante"] : '';

                 if ($nro_comprobante) {
                    
                     $sql = "SELECT * FROM kardex_almacen  WHERE trim(comprobante) = trim('" . $nro_comprobante . "') " ;
                     $campos = $conn->ObtenerFilasBySqlSelect($sql);
 
                    if (count($campos) == 0) {
                        echo "";
                    } else {
                       echo json_encode($campos);
                    }                     
                 } 
 

            break;

            case "det_kardex_almacen":

                 $id = (isset($_GET["id"])) ? $_GET["id"] : '';

                 if ($id) {
                    $campos = $conn->ObtenerFilasBySqlSelect("SELECT b . * ,  a.cod_item, a.referencia, a.descripcion1, a.cantidad_bulto 
                            FROM kardex_almacen_detalle b
                            INNER JOIN item a ON a.id_item = b.id_item
                            WHERE b.id_transaccion = " . $id );

                    if (count($campos) == 0) {
                        echo "";
                    } else {
                       echo json_encode($campos);
                    }
                 }   
             break;      
            case "verificarChequePorTipoCuentaBanco":
                 $cod_tesor_bandodet = (isset($_GET["cod_tesor_bandodet"])) ? $_GET["cod_tesor_bandodet"] : '';
                 $numeroCheque =  (isset($_GET["numeroCheque"])) ? $_GET["numeroCheque"] : '';

                 $excluir = "";
                 //if($cod_tesor_bandodet) {
                    $campos = $conn->ObtenerFilasBySqlSelect("
                        SELECT 
                            c.* 
                        FROM 
                            cheque c 
                        INNER JOIN 
                            chequera ON c.cod_chequera = chequera.cod_chequera 
                        AND 
                            chequera.situacion= 'A' 
                        WHERE  
                            chequera.cod_tesor_bandodet = '{$cod_tesor_bandodet}' 
                        AND 
                            c.situacion = 'A' 
                        AND c.cod_cheque =  (
                                        SELECT min(c.cod_cheque) FROM cheque c 
                                        inner join chequera on c.cod_chequera = chequera.cod_chequera and 
                                        chequera.situacion='A' 
                                        where  chequera.cod_tesor_bandodet = '{$cod_tesor_bandodet}' and c.situacion = 'A' and c.cheque = '{$numeroCheque}'
                                        )");
                    
                        if (count($campos[0]['cheque']) == "") {
                            echo "";
                        } else {
                           echo json_encode($campos[0]);
                        }
                //}

                break;
            case "chequesPorTipoCuentaBanco":
                 $cod_tesor_bandodet = (isset($_GET["cod_tesor_bandodet"])) ? $_GET["cod_tesor_bandodet"] : '';
                 $not_in = (isset($_GET["num_cheques_excluir"])) ? $_GET["num_cheques_excluir"] : '';
                 $excluir = "";
                 if($cod_tesor_bandodet){
                    if(!empty($not_in)){
                        $excluir = " and cheque not in (".$not_in.")";
                    }
                    $campos = $conn->ObtenerFilasBySqlSelect("
                        SELECT 
                            c.* 
                        FROM 
                            cheque c 
                        INNER JOIN 
                            chequera ON c.cod_chequera = chequera.cod_chequera 
                        AND 
                            chequera.situacion= 'A' 
                        WHERE  
                            chequera.cod_tesor_bandodet = '{$cod_tesor_bandodet}' 
                        AND 
                            c.situacion = 'A' 
                        AND c.cod_cheque =  (
                                        SELECT min(c.cod_cheque) FROM cheque c 
                                        inner join chequera on c.cod_chequera = chequera.cod_chequera and 
                                        chequera.situacion='A' 
                                        where  chequera.cod_tesor_bandodet = '{$cod_tesor_bandodet}' and c.situacion = 'A' ".$excluir."
                                        )");

                        if (count($campos) == 0) {
                            echo "";
                        } else {
                           echo json_encode($campos);
                        }
                }
             break;
             
            case "anular_ing_mercancia_stock":

                 $id = (isset($_GET["id"])) ? $_GET["id"] : '';

                 if ($id) {
                    $item = $conn->ObtenerFilasBySqlSelect("SELECT b . * 
                            FROM kardex_almacen_detalle b
                            WHERE b.id_transaccion = " . $id );

                    if (count($item) == 0) {
                        echo "";
                    } else {
                        echo "";
                           foreach ($item as $key => $campos) {
                                if ($campos['id_item'] > 0) {
                                    $item_update = $conn->ObtenerFilasBySqlSelect("SELECT b . * 
                                           FROM item b
                                           WHERE b.id_item = " . $campos['id_item'] );                                   
                                    
                                     $instruccion = "UPDATE item
                                             SET costo_anterior =  '0' , 
                                                    costo_actual = '{$item_update[0]['costo_anterior']} ' ,
                                                    costo_cif = '{$item_update[0]['costo_anterior']} '   ,
                                                    costo_origen = '{$item_update[0]['costo_anterior']} '    
                                             WHERE id_item  = '{$campos['id_item']} ';";
                                    $conn->Execute2($instruccion);      

                                    $item_almacen = $conn->ObtenerFilasBySqlSelect("SELECT * 
                                                                        FROM item_existencia_almacen 
                                                                        WHERE id_item  = '{$campos['id_item']}' AND cod_almacen = '{$campos['id_almacen_entrada']}' ;" );  
                                            
                                    if (count($item_almacen[0]) > 0) { 
                                    echo "exis " .   $cantidadExistente = $item_almacen[0]["cantidad"];
                                    echo "total " .    $cantidadMovim = $cantidadExistente - $campos['cantidad'];

                                        $intruccion_up = "UPDATE item_existencia_almacen 
                                            SET cantidad = '$cantidadMovim'
                                            WHERE id_item  = '{$campos['id_item']}' AND cod_almacen = '{$campos['id_almacen_entrada']}';";
                                        
                                       $conn->Execute2($intruccion_up);       
                                    }
                              
                                }
                            }

                                    $instruccion = "delete from kardex_almacen WHERE id_transaccion = " . $id;
                                    $conn->Execute2($instruccion);

                                    $instruccion = "delete from kardex_almacen_detalle WHERE id_transaccion = " . $id;
                                    $conn->Execute2($instruccion);                            
                       echo json_encode($campos);
                    }
                 }   
             break;               
             
           case "getPermisosSubseccion":
                 
                 $cod_seccion = (isset($_GET["cod_seccion"])) ? $_GET["cod_seccion"] : '';
                 $clase = (isset($_GET["clase"])) ? $_GET["clase"] : '';
                         
                 if ($cod_seccion) {
                     
                     $sql = "SELECT a.*,  b.clase FROM  subseccion_usuario  a "
                                . "INNER JOIN subseccion b ON b.cod_subseccion = a.cod_subseccion "
                                . "WHERE a.cod_usuario=".$_SESSION['cod_usuario'] ." and "
                                . " a.cod_modulo = ".$cod_seccion ;
                 
                     $campos = $login->ObtenerFilasBySqlSelect($sql);
                     
                       if (count($campos) == 0) {
                           echo "";
                       } else {
                          echo json_encode($campos);
                       }     
                       
                 } 

            break;

           case "getPermisosDetalle":

                $cod_permiso = (isset($_GET["cod_permiso"])) ? $_GET["cod_permiso"] : '';
                $cod_usuario = (isset($_GET["cod_usuario"])) ? $_GET["cod_usuario"] : '';
                
                 if ($cod_permiso) {
                     
                     $sql = " SELECT * FROM  permiso_usuario_detalle  "
                             . " WHERE cod_usuario=".$cod_usuario . " and "
                            . " cod_permiso_detalle = ". $cod_permiso ;
                 
                     $campos = $login->ObtenerFilasBySqlSelect($sql);
                     
                       if (count($campos) == 0) {
                           echo "-1";
                       } else {
                          echo json_encode($campos);
                       }     
                       
                 } 

            break;
            case "seleccionarFacturaDetalPendiente":

                $id_facturas_detals = $_GET["id_facturas_detals"];

                $sql = "SELECT * FROM factura_detal_detalle fdd inner join item i on i.id_item = fdd.id_item WHERE fdd.id_factura_detal in ({$id_facturas_detals});";
                $factura_detal_detalles = $conn->ObtenerFilasBySqlSelect($sql);
                $i=0;
                $_factura_detal_detalles = array();
                foreach ($factura_detal_detalles as $factura_detal_detalle) {
                    // $talla_color = "";
                    $_factura_detal_detalles [] = $factura_detal_detalle;
                    // if($factura_detal_detalle["_posee_talla_color"]=="si"){
                    //     $id_detalle_pedido = $factura_detal_detalle["id_detalle_pedido"];
                    //     $sql = "SELECT ptc.* FROM factura_detal_detalle_cantidad_talla_color ptc WHERE ptc.id_detalle_pedido = {$id_detalle_pedido};";
                    //     $talla_color = $conn->ObtenerFilasBySqlSelect($sql);
                    //     $registros_factura_detal_detalles[$i]["posee_talla_color"] = "si";
                    //     $registros_factura_detal_detalles[$i]["cantidad_por_talla_y_color"] = json_encode($talla_color);
                    // }else{
                    //     $registros_factura_detal_detalles[$i]["posee_talla_color"] = "no";
                    // }
                    // $i++;
                }

                echo json_encode(array(
                    "productos" => $_factura_detal_detalles
                    ));
                break;
            case "verificarAcceso":
                
                $contrasena = (isset($_GET["clave"])) ? $_GET["clave"] : '';
                $usuario = (isset($_GET["cod_usuario"])) ? $_GET["cod_usuario"] : '';
                
                if($contrasena and $usuario){
                        $contrasena = md5($contrasena);
                        $sql = "SELECT cod_usuario FROM usuarios  WHERE cod_empresa = '" . $_SESSION['id_empresa'] . "' and usuario = '" . $usuario . "'  and clave   = '" . $contrasena . "'" ;

                        $campos = $login->ObtenerFilasBySqlSelect($sql);
                }
                
                      if (count($campos) == 0) {
                           echo "";
                       } else {
                          echo json_encode($campos);
                       }  
                
            break;
            case "filtro_facturas_detal":
                $fecha_inicio = $_POST["fecha_inicio"];
                $fecha_fin = $_POST["fecha_fin"];
                $sql = "
                    select 
                        i.cod_item,
                        i.descripcion1 as descripcion, 
                        tdd.id_factura_detal,
                        tdd.id_item,
                        tdd._item_almacen,
                        tdd._item_descripcion,
                        tdd._id_costo_actual,
                        tdd._item_preciosiniva, 
                        sum(tdd._item_totalsiniva) as _item_totalsiniva,
                        sum(tdd._item_totalconiva) as _item_totalconiva,   
                        sum(tdd._item_cantidad) as _item_cantidad,
                        tdd._item_descuento,
                        tdd._item_montodescuento,
                        tdd._item_piva,
                        tdd._cantidad_bulto,
                        tdd._cantidad_bulto_kilos,
                        tdd._unidad_empaque,
                        tdd._ganancia_item_individual,
                        tdd._porcentaje_ganancia,
                        tdd._totalm3,
                        tdd._totalft3,
                        tdd._peso_total_item,
                        tdd._posee_talla_color,
                        tdd.usuario_creacion,
                        tdd.fecha_creacion,
                        tdd.anulado,
                        'si' as es_producto_detal,
                        '{$fecha_inicio}' as fecha_inicio, 
                        '{$fecha_fin}' as fecha_fin, 
                        false as _checked,
                        i.*
                    from 
                       factura_detal td 
                    inner join 
                       factura_detal_detalle tdd on td.id_factura_detal = tdd.id_factura_detal
                    inner join 
                       item i on i.id_item = tdd.id_item
                    where tdd.checked=0 and td.fechaFactura between '{$fecha_inicio}' and '{$fecha_fin}'
                    group by tdd.id_item
                ";
                $lista_items_factura_detal = $conn->ObtenerFilasBySqlSelect($sql);
                if (count($lista_items_factura_detal) == 0) {
                    echo json_encode(array());
                } else {
                    echo json_encode($lista_items_factura_detal);
                }    
            break;
            case "obtener_seriales":
                $id_item = $_POST["id_item"];
                $filtro = @$_POST["filtro"];
                $DISPONIBLE = 1;

                $sql = "SELECT * FROM item_seriales WHERE id_item = $id_item and status = $DISPONIBLE";

                if(!empty($filtro)){
                    $sql .= " and UPPER(serial) like UPPER('%".$filtro."%')";
                }
                
                $collection = $conn->ObtenerFilasBySqlSelect($sql);
                print json_encode(array(
                        "count" => count($collection),
                        "collection" => $collection
                    ));
            break;
            case "clonarCotizacion":
                $cliente = $_GET[cliente];
                $pedido = $_GET[cotizacion_id];
                $usuario = $login->getUsuario();
                $conn->BeginTrans();

                $correlativos = new Correlativos();

                $instruccion = "INSERT INTO cotizacion_presupuesto (`cod_cotizacion`, `id_cliente`, `cod_vendedor`, `id_factura`, `fecha_cotizacion`, `subtotal`, `descuentosItemCotizacion`, `montoItemsCotizacion`, `ivaTotalCotizacion`, `TotalTotalCotizacion`, `cantidad_items`, `totalizar_sub_total`, `totalizar_descuento_parcial`, `totalizar_total_operacion`, `totalizar_pdescuento_global`, `totalizar_descuento_global`, `totalizar_base_imponible`, `totalizar_monto_iva`, `totalizar_total_general`, `totalizar_total_retencion`, `cod_estatus`, `fecha_pago`, `fecha_creacion`, `usuario_creacion`, `formapago`, `id_divisa`, `observacion`, `termino_pago_id`)  
                (select  `cod_cotizacion`, `id_cliente`, `cod_vendedor`, `id_factura`, `fecha_cotizacion`, `subtotal`, `descuentosItemCotizacion`, `montoItemsCotizacion`, `ivaTotalCotizacion`, `TotalTotalCotizacion`, `cantidad_items`, `totalizar_sub_total`, `totalizar_descuento_parcial`, `totalizar_total_operacion`, `totalizar_pdescuento_global`, `totalizar_descuento_global`, `totalizar_base_imponible`, `totalizar_monto_iva`, `totalizar_total_general`, `totalizar_total_retencion`, `cod_estatus`, `fecha_pago`, `fecha_creacion`, `usuario_creacion`, `formapago`, `id_divisa`, `observacion`, `termino_pago_id` FROM cotizacion_presupuesto WHERE id_cotizacion = '$pedido') ";

                $conn->ExecuteTrans($instruccion);

                $idPedido = $conn->getInsertID(); 


                $nro_pedido = $correlativos->getUltimoCorrelativo("cod_cotizacion", 0, "si");
                $formateo_nro_factura = $nro_pedido;

                $instruccion = "UPDATE cotizacion_presupuesto  SET id_cliente='$cliente', cod_cotizacion='$nro_pedido', fecha_cotizacion=curdate(), fecha_creacion=CURRENT_TIMESTAMP, usuario_creacion = '$usuario' WHERE id_cotizacion = '$idPedido' ";
                $conn->ExecuteTrans($instruccion);

                $instruccion = "INSERT INTO cotizacion_presupuesto_detalle (id_cotizacion, id_item, _item_almacen, _item_descripcion, _item_cantidad, _item_preciosiniva, _item_descuento, _item_montodescuento, _item_piva, _item_totalsiniva, _item_totalconiva, usuario_creacion, fecha_creacion) 
(select '".$idPedido."',id_item, _item_almacen, _item_descripcion, _item_cantidad, _item_preciosiniva, _item_descuento, _item_montodescuento, _item_piva, _item_totalsiniva, _item_totalconiva, '$usuario', CURRENT_TIMESTAMP FROM cotizacion_presupuesto_detalle WHERE id_cotizacion = '$pedido') ";

                $conn->ExecuteTrans($instruccion);

                 $nro_pedido = $correlativos->getUltimoCorrelativo("cod_cotizacion", 1, "no");
                $conn->ExecuteTrans("update correlativos set contador = '" . $nro_pedido . "' where campo = 'cod_cotizacion'");
                $nro_pedido -= 1;

            break;
            case "clonarPedido":

                $cliente = $_GET[cliente];
                $pedido = $_GET[pedido];
                $usuario = $login->getUsuario();
                $conn->BeginTrans();

                $correlativos = new Correlativos();

                $instruccion = "INSERT INTO pedido(id_pedido,  cod_pedido, id_cliente, cod_vendedor, id_factura, fechaPedido, subtotal, descuentosItemPedido, montoItemsPedido, ivaTotalPedido, TotalTotalPedido, cantidad_items, totalizar_sub_total, totalizar_descuento_parcial, totalizar_total_operacion, totalizar_pdescuento_global, totalizar_descuento_global, totalizar_base_imponible, totalizar_monto_iva, totalizar_total_general, totalizar_total_retencion, formapago, cod_estatus, fecha_pago, total_bultos, peso_total_item, total_m3, total_ft3, total_porcentaje_ganancia, total_monto_ganancia_total, validar_stock, fecha_creacion, usuario_creacion, observacion) SELECT '', cod_pedido, id_cliente, cod_vendedor, id_factura, fechaPedido, subtotal, descuentosItemPedido, montoItemsPedido, ivaTotalPedido, TotalTotalPedido, cantidad_items, totalizar_sub_total, totalizar_descuento_parcial, totalizar_total_operacion, totalizar_pdescuento_global, totalizar_descuento_global, totalizar_base_imponible, totalizar_monto_iva, totalizar_total_general, totalizar_total_retencion, formapago, cod_estatus, fecha_pago, total_bultos, peso_total_item, total_m3, total_ft3, total_porcentaje_ganancia, total_monto_ganancia_total, validar_stock, fecha_creacion, usuario_creacion, observacion FROM pedido WHERE id_pedido = '$pedido' ";

                $conn->ExecuteTrans($instruccion);

                $idPedido = $conn->getInsertID(); 


                $nro_pedido = $correlativos->getUltimoCorrelativo("cod_pedido", 0, "si");
                $formateo_nro_factura = $nro_pedido;

                $instruccion = "UPDATE pedido  SET id_cliente='$cliente', cod_pedido='$nro_pedido', fechaPedido=CURRENT_TIMESTAMP, fecha_creacion=CURRENT_TIMESTAMP, usuario_creacion = '$usuario' WHERE id_pedido = '$idPedido' ";
                $conn->ExecuteTrans($instruccion);
                  

                $instruccion = "INSERT INTO kardex_almacen (tipo_movimiento_almacen, autorizado_por, fecha, usuario_creacion, fecha_creacion, estado, fecha_ejecucion, id_documento, cod_proveedor, comprobante, anio, tipo_costo, estatus, observacion) values ('2','" . $login->getUsuario() . "', CURRENT_TIMESTAMP, '" . $login->getUsuario() . "', CURRENT_TIMESTAMP, 'Pendiente', CURRENT_TIMESTAMP, 0, 0, '00000000', '0', 'cif',0, 'Salida por ventas')";
                $conn->ExecuteTrans($instruccion);

                $idKardex = $conn->getInsertID();   


                $consulta="SELECT * FROM pedido_detalle WHERE id_pedido='".$pedido."'";
                $pedidoDetalles = $conn->ObtenerFilasBySqlSelect($consulta);
                
                $codBar = '';
                $kana = 0;
                foreach($pedidoDetalles as $key => $detalles)
                {
                    $idItem = $detalles[id_item];
                    $canti = $detalles[_item_cantidad];

                    $consulta="SELECT codigo_barras FROM item WHERE id_item='".$idItem."'";
                    $items = $conn->ObtenerFilasBySqlSelect($consulta);
                    $codBarras=$items[0][codigo_barras];

                    $consulta="SELECT * FROM item_existencia_almacen WHERE id_item='".$idItem."' and cod_almacen='".$parametros_generales[0][cod_almacen]."'";
                    $existencia = $conn->ObtenerFilasBySqlSelect($consulta);
                    $cant=$existencia[0][cantidad];
                    
                    if($cant>=$canti)
                    {
                        $instruccion = "UPDATE item_existencia_almacen SET cantidad = cantidad-".$canti." WHERE id_item='".$idItem."' and cod_almacen='".$parametros_generales[0][cod_almacen]."'";
                    }
                    elseif($cant<$canti)
                    {
                        $canti = $cant;
                        $instruccion = "UPDATE item_existencia_almacen SET cantidad = '0' WHERE id_item='".$idItem."' and cod_almacen='".$parametros_generales[0][cod_almacen]."'";

                        $codBar .= $codBarras.', ';
                    }
                    elseif($cant<=0) 
                    {
                        $codBar .= $codBarras.', ';
                        continue;
                    }
                    $conn->ExecuteTrans($instruccion);

                    $instruccion = "INSERT INTO pedido_detalle (id_detalle_pedido, id_pedido, id_item, cod_item, _item_almacen, _item_descripcion, _item_cantidad, _item_preciosiniva, _item_descuento, _item_montodescuento, _item_piva, _item_totalsiniva, _item_totalconiva, _cantidad_bulto, _cantidad_bulto_kilos, _unidad_empaque, _ganancia_item_individual, _porcentaje_ganancia, _totalm3, _totalft3, _peso_total_item, _posee_talla_color, usuario_creacion, fecha_creacion) select '', $idPedido, id_item, cod_item, _item_almacen, _item_descripcion, _item_cantidad, _item_preciosiniva, _item_descuento, _item_montodescuento, _item_piva, _item_totalsiniva, _item_totalconiva, _cantidad_bulto, _cantidad_bulto_kilos, _unidad_empaque, _ganancia_item_individual, _porcentaje_ganancia, _totalm3, _totalft3, _peso_total_item, _posee_talla_color, usuario_creacion, fecha_creacion FROM pedido_detalle WHERE id_detalle_pedido = '".$detalles[id_detalle_pedido]."' ";
                    $conn->ExecuteTrans($instruccion);
                    
                    $instruccion = "INSERT INTO kardex_almacen_detalle(id_transaccion, id_almacen_entrada, id_almacen_salida, id_item, cantidad, cantidad_distribuida, precio) VALUES ('$idKardex', '0','".$parametros_generales[0][cod_almacen]."', '$idItem', '".$canti."', '0', '".$detalles[_item_preciosiniva]."')";
                    $conn->ExecuteTrans($instruccion);
                    
                    $kana = 1;
                }
                
                $nro_pedido = $correlativos->getUltimoCorrelativo("cod_pedido", 1, "no");
                $conn->ExecuteTrans("update correlativos set contador = '" . $nro_pedido . "' where campo = 'cod_pedido'");
                $nro_pedido -= 1;
            
                if($kana==0)
                {
                    
                    $conn->CommitTrans(0);
                }
                else
                {
                    
                      
                    $conn->CommitTrans($conn->errorTransaccion);
                }
                
            break;
         
    }
}
?>
