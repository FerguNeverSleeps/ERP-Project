<?php
class Trafico extends ConexionComun{

	var $LimitePaginaciones = 20;
   private $id;
   private $puertos_salida_id;
   private $puertos_destino_id;
   private $tipo_carga_id;
   private $paises_id;
   private $observaciones;
   private $transporte_via;
   private $codigo_transporte;
   private $embarque_guia;
   private $fecha;
   private $fecha_factura;
   private $factura_comercial;
   private $destinatario;
   private $direccion_destinatario;
   private $transporte;
   private $clave;
   private $consignado;
   private $embarcador;
   private $direccion_embarcador;
   private $peso_bulto;
   private $cantidad_bulto;
   private $sub_total;
   private $flete;
   private $seguros;
   private $gastos;
   private $total;
   private $tipo_dmc;
   private $fecha_creacion;
   private $usuario_creacion;
   private $items_factura;
   private $cantidad_item;

   function __construc() {
        parent::__construct();
   }

   /**
   * Funcion prepare: Prepara el objeto trafico para guardar los elementos
   * @param (string) $data: array POST con los datos que se guardaran
   * @param (string) $tipo_trafico: tipo de trafico que que tendra el registro (salida, trapaso, entrada)
   */
   function prepare($data, $tipo_trafico){
      $this->tipo_dmc = $tipo_trafico;
      $this->items_factura = $data['items'];
      $this->usuario_creacion = $data['usuario'];
      $this->clave = isset($data['clave']) ? $data['clave'] : '';
      $this->consignado = isset($data['consignado']) ? $data['consignado'] : '';
      $this->embarcador = isset($data['embarcador']) ? $data['embarcador'] : '';
      $this->transporte = isset($data['transporte']) ? $data['transporte'] : '';
      $this->tipo_carga_id = isset($data['tipo_carga']) ? $data['tipo_carga'] : '';
      $this->paises_id = isset($data['pais_destino']) ? $data['pais_destino'] : '';
      $this->fecha = isset($data['fecha_registro']) ? $data['fecha_registro'] : '';
      $this->destinatario = isset($data['destinatario']) ? $data['destinatario'] : '';
      $this->observaciones = isset($data['observaciones']) ? $data['observaciones'] : '';
      $this->embarque_guia = isset($data['embarque_guia']) ? $data['embarque_guia'] : '';
      $this->fecha_factura = isset($data['fecha_factura']) ? $data['fecha_factura'] : '';
      $this->transporte_via = isset($data['transporte_via']) ? $data['transporte_via'] : '';
      $this->puertos_salida_id = isset($data['puerto_salida']) ? $data['puerto_salida'] : '';
      $this->puertos_destino_id = isset($data['puerto_destino']) ? $data['puerto_destino'] : '';
      $this->direccion_embarcador = isset($data['dir_embarcador']) ? $data['dir_embarcador'] : '';
      $this->codigo_transporte = isset($data['codigo_transporte']) ? $data['codigo_transporte'] : '';
      $this->factura_comercial = isset($data['factura_comercial']) ? $data['factura_comercial'] : '';
      $this->direccion_destinatario = isset($data['dir_destinatario']) ? $data['dir_destinatario'] : '';
      $this->flete = isset($data['flete']) ? $data['flete'] : 0.00;
      $this->total = isset($data['total']) ? $data['total'] : 0.00;
      $this->gastos = isset($data['gastos']) ? $data['gastos'] : 0.00;
      $this->seguros = isset($data['seguros']) ? $data['seguros'] : 0.00;
      $this->sub_total = isset($data['sub_total']) ? $data['sub_total'] : 0.00;
      $this->peso_bulto = isset($data['peso_bulto']) ? $data['peso_bulto'] : 0;
      $this->cantidad_bulto = isset($data['cantidad_bulto']) ? $data['cantidad_bulto'] : 0;
      $this->cantidad_item = isset($data['cantidad_item']) ? $data['cantidad_item'] : 0;
   }

   /**
   * Funcion save: Guarda los elementos preparados en el objeto
   */
   function save(){
       $campos = $_POST['campos'];
       $valor = $_POST['valor'];
         
        $sql = "INSERT INTO dmc_vn (" ;
	  
		for($cont = 0; $cont < count($campos); $cont++) 
		{
			$sql = $sql.$campos[$cont] ;
			if($cont != (count($campos) -1)) 
			{
				$sql = $sql."," ;
			}
		}
	
		$sql = $sql.") VALUES (" ;
	
		for($cont= 0; $cont < count($valor); $cont++) 
		{
			$sql = $sql."'".$valor[$cont]."'" ;
	
			if($cont != (count($valor) -1)) {
				$sql = $sql."," ;
			}
		}
	
		$sql = $sql.")" ;
                
                 

      parent::BeginTrans();
      parent::ExecuteTrans($sql);
      $dcm_id = parent::getInsertID();

      $sql = "INSERT INTO dmc_detalle (dcm_id, factura_id, factura_detalle_id, posicion_arancel, unidad, descripcion,
                                       peso_bulto, costo, cantidad_bulto,id_item,total) VALUES (%d, %d, %d, %d, '%s', '%s', %f, %f, %d, %d,%f)";
      foreach ($this->items_factura as $detalle){
         $query = sprintf($sql, $dcm_id, $detalle['factura_id'], $detalle['detalle_id'],
                        $detalle['posicion_arancel'], $detalle['unidad_empaque'], addslashes($detalle['descripcion']),
                        $detalle['peso_bulto'], $detalle['costo'], $detalle['cantidad_bulto'], $detalle['Facturaid'],$detalle['totalf']);
         parent::BeginTrans();
         parent::ExecuteTrans($query);
         parent::CommitTrans(parent::getErrorTransaccion());
         $queryAF = "update factura set DMC_Procesado='YES' where id_factura='". $detalle['factura_id']."'" ;
         parent::BeginTrans();
         parent::ExecuteTrans($queryAF);
         parent::CommitTrans(parent::getErrorTransaccion());
         $Fileprint = date('Ymdhis') ;
         $sql = "SELECT medio_transporte , tipo_vagon FROM dmc_vn WHERE id = '".$dcm_id."'";
         
         $tipo_via = $this->ObtenerFilasBySqlSelect($sql);
         $datotras = $tipo_via[0]['medio_transporte'] ;
         $tipvagon = $tipo_via[0]['tipo_vagon'] ; 
         $desc_tipo = "exit"; 
         $rutasvar = '/tmp/' ;
         $fechaf = date('Ymdhis') ;
         $sql_doc = "select 'DMCFORM|$Fileprint' into outfile '".$rutasvar."DMCFORM_"."_".$fechaf.".txt'
		fields TERMINATED BY ''
		ENCLOSED BY ''
		LINES TERMINATED BY '\r\n'
		" ;
         parent::BeginTrans();
         parent::ExecuteTrans($sql_doc);
         parent::CommitTrans(parent::getErrorTransaccion());
         $sql1 = "SELECT  'DTLS', '".$desc_tipo."' tipo,
	referencia_formulario,n_contacto_compania,'".$datotras."' otrod,
        nombre_embarque,n_documento_transporte,compania_transporte,'','',tipo_vagon, (select Codigo from puertos where Cod =dmc_vn.puerto_origen) puerto_origen, (select Codigo from puertos where Cod =dmc_vn.puerto_trasbordo) puerto_trasbordo,  
	(select Codigo from puertos where Cod =dmc_vn.puerto_destino) puerto_destino, '',pais_destino, (select Codigo  from puertos where Cod =dmc_vn.puerto_salida) puerto_salida,pais_consignado,n_documento_control,
	'','','','','',pais_manufacturacion,consumo_propio,re_importacion,'','','',descuento,otro_gasto,comentario,''
	from dmc_vn  where id='".$dcm_id."'";
 $veccad = array('DTLS', 'tipo',
	'referencia_formulario','n_contacto_compania','otrod',
        'nombre_embarque','n_documento_transporte','compania_transporte','','','tipo_vagon','puerto_origen','puerto_trasbordo',  
	'puerto_destino', '','pais_destino','puerto_salida','pais_consignado','n_documento_control',
	'','','','','','pais_manufacturacion','consumo_propio','re_importacion','','','','descuento','otro_gasto','comentario','') ;
        $datax = $this->ObtenerFilasBySqlSelect($sql1);
	$filas1 = array();
	$f = 0;				
	foreach ($datax as $puertos)
        {
              
            $filas1[$f] = "1";
            for($x=0;$x<count($puertos);$x++) 
            {							
	      $filas1[$f] = $filas1[$f] ."|" . $puertos[$veccad[$x]]  ;						
            }					
            $f++;
         
      }
          
      $sql3 = "SELECT DISTINCT 'INVOICE', fac.fechaFactura, dmc_de.factura_id, 'UsuarioSession',
          (select Codigo from puertos where Cod =dmc_vn.puerto_salida) puerto_salida, 'receipt.others', 'USD','', 'CambiarPorTotal'  FROM
									dmc_detalle dmc_de
									JOIN dmc_vn dmcvn ON dmcvn.id = dmc_de.dcm_id 
									JOIN factura fac ON dmc_de.factura_id = fac.id_factura
									JOIN factura_detalle facd ON facd.id_factura = fac.id_factura
									WHERE dmc_de.dcm_id = '".$dcm_id."' ";
      
      
       $veccadx = array('INVOICE','fechaFactura', 'factura_id', 'UsuarioSession','puerto_salida', 'receipt.others', 'USD','', 'CambiarPorTotal');
      
      
      	  $dataxx = $this->ObtenerFilasBySqlSelect($sql3);
				$filas3 = array();
				$f = 0;				
                                foreach ($dataxx as $puertoss)
                                {						
                                    $filas3[$f] = "1";
				    for($x=0;$x<count($puertoss);$x++) 
				    {							
					$filas3[$f] = $filas3[$f] ."|" . $puertoss[$veccadx[$x]] ;						
				    }					
						$f++;
				}
      
      
      
      $sql2 = "SELECT 'ITEM',A.posicion_arancel,B.cod_item, A.descripcion,'Pais_Origen','CantidadBoxes','TipoBoxes',A.cantidad_bulto,								
		'UoM_Unidad_Suelta','N', A.peso_bulto,A.total,''
                FROM dmc_detalle A  join item B on (A.id_item = B.id_item)
		WHERE  A.dcm_id = '".$dcm_id."'  ";
	
 $veccadxx = array('ITEM','posicion_arancel','cod_item','descripcion','Pais_Origen','CantidadBoxes','TipoBoxes','cantidad_bulto','UoM_Unidad_Suelta','N', 'peso_bulto','total','');
      
      
      	  $dataxxx = $this->ObtenerFilasBySqlSelect($sql2);                                            
      
                                                                
      
			$filas = array();
			$f = 0;
			
			foreach ($dataxxx as $puertosss)
                        {		
					$filas[$f] = "1";
					for($x=0;$x<count($puertosss);$x++) 
					{
						$filas[$f] = $filas[$f] ."|" . $puertosss[$veccadxx[$x]] ;						
					}					
					$f++;
			}
      
      
      
                        
                       
				
				 
				if ($tipvagon == "cntr")
                                {					
					$sql4 = "SELECT 'CONTAINER' A, '' B , v.valor_mostrar , '' C, '' D, '' E ,'2014-01-01T01:00:00' F
					FROM dmc_vn z
						JOIN dmc_tipo_vagon v ON z.tipo_vagon = v.tipo_vagon 
					WHERE z.id = '".$dcm_id."' ";
				}
				elseif ($tipvagon == "bulkcargo"){					
						$sql4 = "SELECT 'BULKCARGO' A, '' B, '' C, '2014-01-01T01:00:00' D ";				
				}else{
						$sql4 = "SELECT '' A , '' B, '' C, '2014-01-01T01:00:00'D ";								
				}
				  $dataxxxx = $this->ObtenerFilasBySqlSelect($sql4);  
                                  
                                    
     $veccadxxx = array('A','B','C','D','E','F');
 
                                  
                                  
 					$filas4 = array();
					$f = 0;				
					 foreach ($dataxxxx as $puertossss){ 								
							$filas4[$f] = "1";
							for($x=0;$x<count($puertossss);$x++) 
							{							
								$filas4[$f] = $filas4[$f] ."|" . $puertossss[$veccadxxx[$x]];						
							}					
							$f++;
					}
      
      
      
      
      
      
      
      
      
      
      
      
      
       $archivo =  $rutasvar."DMCFORM_".$fechaf.".txt";  
       $fch= fopen($archivo, "a+");  
       fwrite($fch, 'DMCFORM|'.$fechaf);
        fwrite($fch, "\r\n"); 
        for ($pag = 0; $pag < count($filas1); $pag ++)
			{				
				fwrite($fch, $filas1[$pag]);  
				fwrite($fch, "\r\n"); 
				$lineas ++;
			}					
			
                        
                        
                 for ($pag = 0; $pag < count($filas); $pag ++)
			{				
				fwrite($fch, $filas[$pag]); // Grabas
				fwrite($fch, "\r\n"); 
				$lineas ++;
			}
			for ($pag = 0; $pag < count($filas3); $pag ++)
			{				
				fwrite($fch, $filas3[$pag]); // Grabas
				fwrite($fch, "\r\n"); 
				$lineas ++;
			}       
                        
          for ($pag = 0; $pag < count($filas4); $pag ++)
			{				
				fwrite($fch, $filas4[$pag]); // Grabas
				fwrite($fch, "\r\n"); 
				$lineas ++;
			}              
                        
                        
         fwrite($fch, "DMCFORM|" . $lineas); // Grabas
			fclose($fch); // Cierras el archivo.
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
         
      }
      if (parent::getErrorTransaccion() == 1){
         parent::CommitTrans(parent::getErrorTransaccion());
         return true;
      }else
         return false;
   }

   /**
   * Funcion que obtiene todas las facturas registradas
   * @return (SQL) data: String con el sql
   */
   function buscar_todas_facturas() {
   	$sql = "SELECT f.id_factura, f.fechaFactura, f.cod_factura, f.facturar_a, f.TotalTotalFactura,
            c.cod_cliente, c.nombre
            FROM factura AS f INNER JOIN clientes AS c ON c.id_cliente = f.id_cliente and f.DMC_Procesado= 'NO' order by f.id_factura desc ";
      return $sql;
   }

   /**
   * Funcion que obtiene la factura y con sus relaciones
   * @@param (int) $id_factura: clave primaria de la factura
   * @return (array) data: array con los datos obtenidos
   * EJm: Array(
   *         id_factura => "6"
   *         id_detalle_factura => "4"
   *        _unidad_empaque => "DOC"
   *        _item_descripcion => "CHANCLAS P/ HOMBRES"
   *        _cantidad_bulto => "5"
   *        _cantidad_bulto_kilos => "12"
   *        _id_costo_actual => "0.000000"
   *        _item_cantidad => "15"
   *         unidad_empaque => "DOC"
   *         posicion_arancel => "28")
   */
   function buscar_factura($id_factura){
      $sql = "SELECT f.id_factura, fd.id_detalle_factura, fd._unidad_empaque, fd._item_descripcion, fd._cantidad_bulto,
               fd._cantidad_bulto_kilos, fd._id_costo_actual, fd._item_cantidad, it.unidad_empaque, it.posicion_arancel,fd.id_item ,fd._item_totalsiniva";
      $sql .= " FROM factura as f";
      $sql .= " INNER JOIN factura_detalle as fd ON fd.id_factura = f.id_factura";
      $sql .= " INNER JOIN item AS it ON fd.id_item = it.id_item";
      $sql .= " WHERE f.id_factura = %d";
      $sql = sprintf($sql, $id_factura);
      $data = $this->ObtenerFilasBySqlSelect($sql);
      return $data;
   }

   /**
   * Funcion que suma todas las cantidad de bulto y cantidad de items
   * @param (array) $ids_facturas: array con las clave primaria de las facturas
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array(
   *            cantidad_item => "300"
   *            cantidad_bulto => "200"
   *            )
   */
   function sumar_cantidades($ids_facturas){
      $string_in = "(%s)";
      $data = join(", ",  array_values($ids_facturas));
      $string_in = sprintf($string_in, $data);
      $sql = "SELECT SUM( _item_cantidad ) AS cantidad_item, SUM( _cantidad_bulto ) AS cantidad_bulto";
      $sql.= " FROM factura_detalle WHERE id_factura IN %s ";
      $sql = sprintf($sql, $string_in);
      $data = $this->ObtenerFilasBySqlSelect($sql);
      return $data[0];
   }

   /**
   * Funcion que lista los paises registrados
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array (
   *           [id] => Array(1, 2)
   *           [nombre] => Array("Afganistán", "Islas Gland")
   *         )
   */
   function listar_paises(){
      $sql = "SELECT Codigo as id, Descripcion as nombre FROM paises_dmc";
      $data = $this->ObtenerFilasBySqlSelect($sql);
      
    
$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");

     
      
       foreach ($data as $pais){
         $paises['id'][] = $pais['id'];
         $paises['nombre'][] = str_replace($no_permitidas, $permitidas ,htmlentities($pais['nombre']));
      }
      return $paises;
   }
   
   
    function listar_medio(){
      $sql = "SELECT medio_transporte, valor_mostrar FROM dmc_medio_transporte";
      $data = $this->ObtenerFilasBySqlSelect($sql);
       foreach ($data as $vagones){
         $vagon['medio_transporte'][] = $vagones['medio_transporte'];
         $vagon['valor_mostrar'][] = $vagones['valor_mostrar'];
      }
      return $vagon;
   }
   
   
      function listar_vagon(){
      $sql = "SELECT tipo_vagon, valor_mostrar FROM dmc_tipo_vagon";
      $data = $this->ObtenerFilasBySqlSelect($sql);
       foreach ($data as $vagones){
         $vagon['tipo_vagon'][] = $vagones['tipo_vagon'];
         $vagon['valor_mostrar'][] = $vagones['valor_mostrar'];
      }
      return $vagon;
   }

   /**
   * Funcion que lista los puertos de salida y retorna el resultado
   * @@param (string) $tipo: tipo de puerto que se desea obtener (salida, destino)
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array (
   *         [id] => Array(3, 4)
   *         [nombre] => Array("puerto salida 1", "puerto salida 2")
   *         )
   */
   function listar_puertos($tipo){
     
       
        $sql = "SELECT id, descripcion FROM puertos_salidas"; 
       if($tipo == 'Entrada') 
      $sql = "SELECT id, descripcion FROM puertos_salidas WHERE tipo_puerto = 'Entrada'"; 
      if($tipo == 'salida') 
      $sql = "SELECT id, descripcion FROM puertos_salidas WHERE tipo_puerto = 'Salida'";
        
      $sql = "select Cod as id , Nombre as descripcion from puertos limit 20" ;  
      $sql = sprintf($sql, $tipo);
      $data = $this->ObtenerFilasBySqlSelect($sql);
      foreach ($data as $puertos){
         $puertos_disponibles['id'][] = $puertos['id'];
         $puertos_disponibles['nombre'][] = $puertos['descripcion'];
      }
      return $puertos_disponibles;
   }

   /**
   * Funcion insertar_empresas: crea una lista de empresas por defecto
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array ('id' => 1, 'clave' => 32221, 'descripcion' => "lorem ipsum")
   * NOTA: Son empresas de pruebas extraidas del sistema principal
   */
   function insertar_empresas(){
      $sql = "TRUNCATE TABLE empresas;";
      $sql.= "INSERT INTO empresas(clave, descripcion) VALUES";
      $empresas = array(
               array(5090, 'A DOM PAISA'),
               array(3376, 'ASICSA'),
               array(9999, 'A&M GLOBAL TRADER'),
               array(0004, 'ABRAHAM SERVICE'),
               array(5904, 'ACTION LOGISTIC INC.'),
               array(1759, 'AGALLU INT"L'),
               array(9998, 'AGENCIA DE CARGA COLON'),
               array(5288, 'AL DEPOSITO ZONA LIBRE'),
               array(9997, 'ALIANZA'),
               array(2871, 'ALMACENADORA CARIBEAN'),
               array(9996, 'AMERICA ESTUDIO VENEZUELA'));

      foreach ($empresas as $emp) {
         $value = "(%d, '%s'),";
         $data = sprintf($value, $emp[0], $emp[1]);
         $sql.= " $data";
      }
      $sql = substr($sql, 0, -1);
      $this->Execute2($sql);
      $sql = "SELECT * FROM empresas";
      return $this->ObtenerFilasBySqlSelect($sql);
   }

   /**
   * Funcion listar_empresas: listas las claves y retorna el resultado
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array ('id' => 1, 'codigo' => 32221, 'descripcion' => "lorem ipsum")
   */
   function listar_empresas(){
      $sql = "SELECT * FROM empresas";
      $data = $this->ObtenerFilasBySqlSelect($sql);
      if(count($data) == 0){
         $data = $this->insertar_empresas();
      }
      foreach ($data as $claves){
         $empresas['id'][] = $claves['id'];
         $empresas['descripcion'][] = $claves['descripcion'];
      }
      return $empresas;
   }

      /**
   * Funcion obtener_clave_empresa: salva y busca, retorna los datos obtrnidos
   * @param (int) $clave: string con la clave de la empresa que se deseas buscar
   * @return (array) $this : array con los datos generados
   */
   function obtener_clave_empresa($clave){
      $sql = 'SELECT * from empresas WHERE clave = %s';
      $sql = sprintf($sql, $clave);
      $data = $this->ObtenerFilasBySqlSelect($sql);
      return $data[0];
   }

   /**
   * Funcion insertar_tipos_cargas: inserta los tipos de cargas definidos
   * @return (array) $data : array con los tipos de cargas
   */
   function insertar_tipos_cargas(){
      $cargas = array('CNTR', 'BULKCARGO', 'LUGGAGE', 'OFFSHORE', 'SALES');
      $values = join("'), ('", $cargas);
      $sql = "TRUNCATE TABLE tipos_cargas;
              INSERT INTO tipos_cargas(nombre) VALUES ('$values')";
      $this->Execute2($sql);
      $sql = "SELECT id, nombre FROM tipos_cargas";
      return $this->ObtenerFilasBySqlSelect($sql);
   }

   /**
   * Funcion que lista los tipos de cargas
   * @return (array) $data: array con los datos obtenidos
   * EJm: Array (
   *         [id] => Array(3, 4)
   *         [nombre] => Array("carga 1", "carga  2")
   *         )
   */
   function listar_tipos_cargas(){
      $sql = "SELECT id, nombre FROM tipos_cargas";
      $data = $this->ObtenerFilasBySqlSelect($sql);
      if (count($data) == 0){
         $data = $this->insertar_tipos_cargas();
      }

      foreach ($data as $tipoCarga){
         $tipos_cargas['id'][] = $tipoCarga['id'];
         $tipos_cargas['nombre'][] = $tipoCarga['nombre'];
      }
      return $tipos_cargas;
   }


   /**
   * Funcion obtener_totales: obtiene los totales que se encuentran en una factura
   * @param (array) $ids_facturas: array con las clave primaria de las facturas
   * @return (array) $data: array con los datos obtenidos
   *Array(
   *     [seguro] => 0.00
   *     [flete] => 0.00
   *     [total_gasto] => 30025.00
   *     [total] => 30025.00
   *     [subtotal] => 30025.00
   *     )
   */
   function obtener_totales($ids_facturas){
      $string_in = "(%s)";
      $data = join(", ",  array_values($ids_facturas));
      $string_in = sprintf($string_in, $data);
      $sql = "SELECT SUM(fg.seguro) AS seguro, SUM(fg.flete) AS flete, SUM(fg.total_fob_gasto) AS total_gasto";
      $sql.= ",SUM(f.subtotal) AS subtotal, SUM(TotalTotalFactura) AS total";
      $sql.= " FROM factura AS f";
      $sql.= " INNER JOIN factura_gasto AS fg ON fg.id_factura = f.id_factura";
      $sql.= " WHERE f.id_factura IN %s";
      $sql = sprintf($sql, $string_in);
      $data = parent::ObtenerFilasBySqlSelect($sql);
      return $data[0];
   }

   /**
   * Funcion validar_seleccion: valida si la seleccion de facturas sean correspondientes al usuario autenticado
   * @param (array) $ids_facturas: array con las clave primaria de las facturas
   * @return (bolean) $status: (true, false) si algun dato que selecciono pertenece o no
   */
   function validar_seleccion($ids_facturas, $usuario){
      $sql = "SELECT facturar_a as usuario FROM factura WHERE id_factura = %d";
      $status = true;
      foreach ($ids_facturas as $id) {
         $query = sprintf($sql, $id);
         $data = parent::ObtenerFilasBySqlSelect($query);
         if ($data[0]['usuario'] != $usuario ){
            $status = false;
            break;
         }
      }
      return $status;
   }

   /**
   * Funcion sql_documento: retorna, el sql para la cosulta del dcm
   * @param (array) $ids_facturas: array con las clave primaria de las facturas
   * @return (bolean) $status: (true, false) si algun dato que selecciono pertenece o no
   */
   function sql_documento(){
      $sql = "SELECT d.id, pa.nombre as pais, d.transporte, d.fecha, d.embarcador, d.factura_comercial, d.tipo_dmc, ps.descripcion as origen, ds.descripcion as destino
         FROM dmc as d
         LEFT JOIN puertos_salidas as ps ON d.puertos_salida_id = ps.id AND ps.tipo_puerto='salida'
         LEFT JOIN puertos_salidas as ds ON  d.puertos_destino_id = ds.id AND ds.tipo_puerto='destino'
         LEFT JOIN paises as pa ON  d.paises_id = pa.id";
      return $sql;
   }


   /**
   * Funcion listar_ordenes_compras: retorna los datos gernerados de las ordenes de compra
   * @return (array) $data: retorna un array de los datos que estan registrados
   */
   function listar_ordenes_compras(){
      $sql = "SELECT c.id_compra as id, c.cod_compra, c.fechacompra, c.num_factura_compra, c.concepto, p.descripcion as nombre_proveedo, num_cont_factura as conteo_factura FROM compra as c INNER JOIN proveedores as p ON p.id_proveedor = c.id_proveedor";
      $data = parent::ObtenerFilasBySqlSelect($sql);
      return $data;
   }

   /**
   * Funcion facturas_por_orden_compra: Busca y retornas todas las facturas que tienen ordenes de compra
   * @param (array) $ordenesCompras: array con las ordenes de compra
   * @return (array) $data: facturas que tienen ordenes de compra
   */
   function facturas_por_orden_compra($ordenesCompras){
      $sql ="SELECT";
   }

   /**
   * Funcion buscar_facturas_fecha: Busca y retorna todas las facturas filtradas por un rango de fechas
   * @param (string) $fechaInicio: string con la fecha inicial de busqueda
   * @param (string) $fechaFin: string con la fecha final de busqueda
   * @return (string) $sql : sql con el string generado
   */
   function buscar_facturas_fecha($fechaInicio, $fechaFin = null){
      $meses = array('Enero'=> 1, 'Febrero'=> 2, 'Marzo'=> 3, 'Abril'=> 4
                     ,'Mayo'=> 5, 'Junio'=> 6, 'Julio'=> 7, 'Agosto'=> 8
                     ,'Septiembre' =>9, 'Octubre'=> 10, 'Noviembre'=> 11
                     ,'Diciembre'=> 12);

      if (!empty($fechaInicio) && empty($fechaFin)){
         $fechaInicio = explode(" ", $fechaInicio);
         $fechaInicio = sprintf('%d-%s-%d', $fechaInicio[1], $meses[$fechaInicio[0]], '1');
         $fecha = new DateTime($fechaOption);
         $fecha->modify('last day of this month');
         $fechaFin = $fecha->format('Y-m-d');
      }

      $sql = $this->buscar_todas_facturas();
      if (!empty($fechaInicio) && !empty($fechaFin)){
         $where =" WHERE fechaFactura BETWEEN '%s' AND '%s'";
         $sql.= sprintf($where, $fechaInicio, $fechaFin);
      }
      return $sql;
   }

   /**
   * Funcion buscar_facturas_usuario: Busca y retorna todas las facturas filtradas por usuario
   * @param (string) $usuario: string con el usuario que se desea buscar
   * @return (string) $sql : sql con el string generado
   */
   function buscar_facturas_usuario($usuario){
      $sql = $this->buscar_todas_facturas();
      $where.= " WHERE facturar_a = '%s'";
      $sql.= sprintf($where, $usuario);
      return $sql;
   }




   /*
   *######################################################################################################################################
   *Funcionnes genericas
   *######################################################################################################################################
   */

   function Notificacion() {
      return "<b><img cursor=\"absmiddle\" src=\"../../libs/imagenes/ico_note_1.gif\"> No se encontraron filas en la busqueda.</b>";
   }

   function obtener_num_paginas($consulta) {
      $this->rcampos = $this->ObtenerFilasBySqlSelect($consulta);
         if ($this->rcampos != "") {
            $numero_filas = $this->getFilas();
            $num_paginas = ceil($numero_filas / $this->LimitePaginaciones);
            return $num_paginas;
         } else {
            return 0;
         }
   }

   function obtener_pagina_actual($pagina, $num_paginas) {
      if ($pagina < 1) {
         $pagina = 1;
      }elseif ($pagina > $num_paginas && $num_paginas != 0) {
         $pagina = $num_paginas;
      }
      return $pagina;
   }

   function paginacion($pagina, $consulta) {
      $inicio = ($pagina * $this->LimitePaginaciones) - $this->LimitePaginaciones;
      $sql = $consulta . " limit " . $inicio . ", " . $this->LimitePaginaciones . "";
      return $this->ObtenerFilasBySqlSelect($sql);
   }

}
?>
