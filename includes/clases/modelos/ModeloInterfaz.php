<?php
if(!isset($DIR)){
	$DIR = "../";
}
class ModeloIterfaz{

 // FUNCTIONS
	public function txtRetailBosProducto($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);

			$query = 'select i.codigo_barras,i.estatus,i.descripcion1,i.referencia,i.precio1,i.costo_actual,i.costo_anterior,i.iva,i.monto_exento,i.fecha_creacion from item i inner join item_historico ih on ih.id_item = i.id_item  where ih. status_txt=0 ';
			if($request['rango'] == "fecha"){
				$request['fecha_ini'] = substr($request['fecha_ini'], 6, 4)."-".substr($request['fecha_ini'], 3, 2)."-".substr($request['fecha_ini'], 0, 2);
        		$request['fecha_fin'] = substr($request['fecha_fin'], 6, 4)."-".substr($request['fecha_fin'], 3, 2)."-".substr($request['fecha_fin'], 0, 2);
				$query .= " and ih.fecha_operacion between '".$request['fecha_ini']." 00:00:00' and '".$request['fecha_fin'] ." 23:59:59' ";
			}
			$query .= " group by i.id_item";
			//echo $query;
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			//$matches = Array();
			if($num != 0){
				$archivo = "00000001.TXT";
				$ext_permitidas = array("TXT","txt");
			    
			    //$matches = Array();
			    $ext = "";
			    $iArchivos = 0;
			    $path = "../../../selectraerp/";
			    chmod($path."txt/productos/", 777);
			    $directorio = opendir($path."txt/productos/"); //ruta actual
		        $ext = "";
		        while ($archivo = readdir($directorio)){
		            if (!is_dir($archivo)){
		                $extAux = explode(".", $archivo);
		                if(count($extAux) != 1){
		                    $ext = strtolower($extAux[1]);
		                    if(in_array($ext,$ext_permitidas)){
		                        $iArchivos++;
		                    }    
		                }            
		            }
		        }
		        $archivo = str_pad((($iArchivos == 0)?1:($iArchivos+1)), 8, "0", STR_PAD_LEFT).".TXT";
				$archivo_actualizacion = $path."txt/productos/".$archivo;
				if(!file_exists($archivo_actualizacion)){
		            if($file = fopen($archivo_actualizacion, "w")){
		            	$date = date_create();
						$dateReg = date_format($date, 'YmdH:i:s');
						$dateReg2 = date_format($date, 'Ymd');


						fwrite($file, "1".$dateReg.str_pad($num, 10, "0", STR_PAD_LEFT)."\n\r");




						while ($item = $this->bdControlador->fetch($result)){
								fwrite($file, "2".$item["codigo_barras"].str_pad($item["descripcion1"], 50).str_pad($item["descripcion1"], 20));
								fwrite($file, "00000000"."00000001"."00000000"."00000000"."00000001"."00000001"."00000000"."00000000"."00000000"."00000000"."00000000");
								fwrite($file, $item["referencia"].$item["codigo_barras"]."            "."0000000".str_pad($item["precio1"], 10, " ", STR_PAD_LEFT));
								fwrite($file, str_pad($item["costo_actual"], 10, " ", STR_PAD_LEFT).$item["monto_exento"].str_pad($item["iva"], 5, " ", STR_PAD_LEFT));
								fwrite($file, "0000000".(($item["estatus"]=="A")?1:0)."0000000000".$item["fecha_creacion"].$dateReg2."            ".str_pad($item["costo_anterior"], 10, " ", STR_PAD_LEFT));
								fwrite($file, str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT));
								fwrite($file, $dateReg2."\n\r");
							
						}
						

						fwrite($file, "3".$dateReg.str_pad($num, 10, "0", STR_PAD_LEFT)."\n\r");
				        fclose($file);
		            	
		            }
	            }


	    		$arrayAlmacenes = explode(',',$_POST['arrayAlmacenes']);
	    		for($i = 0; $i < count($arrayAlmacenes);$i++){
		        	$ruta = $path ."txt/productos/".$arrayAlmacenes[$i]."/" ;
					if(!file_exists($ruta)){
						mkdir ($ruta);
					}
					copy($archivo_actualizacion, $ruta.$archivo);
		        }


				$query = 'update item_historico ih set ih.status_txt=1,ih.fecha_generacion_txt=now() where ih.status_txt=0 ';
				if($request['rango'] == "fecha"){
					$request['fecha_ini'] = substr($request['fecha_ini'], 6, 4)."-".substr($request['fecha_ini'], 3, 2)."-".substr($request['fecha_ini'], 0, 2);
	        		$request['fecha_fin'] = substr($request['fecha_fin'], 6, 4)."-".substr($request['fecha_fin'], 3, 2)."-".substr($request['fecha_fin'], 0, 2);
					$query .= " and ih.fecha_operacion between '".$request['fecha_ini']." 00:00:00' and '".$request['fecha_fin'] ." 23:59:59' ";
				}
				$this->bdControlador->setQuery($query);
				$this->bdControlador->ejecutaInstruccion();
				$this->bdControlador->commit();
				$this->bdControlador->desconectar();;
			}	
		    
			
            if($num == 0){
            	$mensaje = "No hay novedades pendientes";
            }
            else{

            	$mensaje = "Se genero el archivo TXT de ".$num." novedad(es)";
            }
			return  Array('success' => true,'mensaje' =>$mensaje ,'novedades' => $num);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getListTxtProductosAlmacen($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);

			$archivo = "00000001.TXT";
			$ext_permitidas = array("TXT","txt");
		    
		    $matches = Array();
		    $archivoArray = Array();
		    $ext = "";
		    $iArchivos = 0;
		    $path = "../../../selectraerp/txt/productos/".$request['bodega']."/";
		    $pathDownload = "/pyme/selectraerp/txt/productos/".$request['bodega']."/";
		    //chmod($path, 777);
		    if (is_dir($path)){
			    $directorio = opendir($path); //ruta actual
		        $ext = "";
		        while ($archivo = readdir($directorio)){
		            if (!is_dir($archivo)){
		                $extAux = explode(".", $archivo);
		                if(count($extAux) != 1){
		                    $ext = strtolower($extAux[1]);
		                    if(in_array($ext,$ext_permitidas)){
				                $arrayTabla['txt'] = $archivo;
				                $arrayTabla['fecha'] = date ("d/m/Y H:i:s.", filemtime($path.$archivo));
				                $arrayTabla['ruta'] = $path.$archivo;
		                        $matches[] = $arrayTabla;
		                    }    
		                }            
		            }
		        }
		    }

			return  Array('success' => true,'mensaje' =>'OK' ,'matches' =>$matches);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}

	public function txtRetailBosRebaja($request,$op){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);

			$query = "select i.codigo_barras,i.estatus,i.descripcion1,i.referencia,rd.precio_nuevo precio1,rd.precio_viejo,i.costo_actual,i.costo_anterior,"
			."i.iva,i.monto_exento,r.fecha_creacion "
			."from rebaja r "
			."inner join rebaja_detalle rd on rd.rebaja_id = r.rebaja_id  "
			."inner join item i on i.id_item = rd.id_item  "
			."where r. status_txt=0 ";

			if($request['rango'] == "fecha"){
				$request['fecha_ini'] = substr($request['fecha_ini'], 6, 4)."-".substr($request['fecha_ini'], 3, 2)."-".substr($request['fecha_ini'], 0, 2);
        		$request['fecha_fin'] = substr($request['fecha_fin'], 6, 4)."-".substr($request['fecha_fin'], 3, 2)."-".substr($request['fecha_fin'], 0, 2);
				$query .= " and r.fecha_creacion between '".$request['fecha_ini']." 00:00:00' and '".$request['fecha_fin'] ." 23:59:59' ";
			}
			//$query .= " group by i.id_item";
			//echo $query;
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			//$matches = Array();
			if($num != 0){
				$archivo = "rebaja-00000001.TXT";
				$ext_permitidas = array("TXT","txt");
			    
			    //$matches = Array();
			    $ext = "";
			    $iArchivos = 0;
			    $path = "../../../selectraerp/";
			    chmod($path."txt/rebajas/", 777);
			    $directorio = opendir($path."txt/rebajas/"); //ruta actual
		        $ext = "";
		        while ($archivo = readdir($directorio)){
		            if (!is_dir($archivo)){
		                $extAux = explode(".", $archivo);
		                if(count($extAux) != 1){
		                    $ext = strtolower($extAux[1]);
		                    if(in_array($ext,$ext_permitidas)){
		                        $iArchivos++;
		                    }    
		                }            
		            }
		        }
		        $archivo = "rebaja-".str_pad((($iArchivos == 0)?1:($iArchivos+1)), 8, "0", STR_PAD_LEFT).".TXT";
				$archivo_actualizacion = $path."txt/rebajas/".$archivo;
				if(!file_exists($archivo_actualizacion)){
		            if($file = fopen($archivo_actualizacion, "w")){
		            	$date = date_create();
						$dateReg = date_format($date, 'YmdH:i:s');
						$dateReg2 = date_format($date, 'Ymd');


						fwrite($file, "1".$dateReg.str_pad($num, 10, "0", STR_PAD_LEFT)."\n\r");




						while ($item = $this->bdControlador->fetch($result)){
								fwrite($file, "2".$item["codigo_barras"].str_pad($item["descripcion1"], 50).str_pad($item["descripcion1"], 20));
								fwrite($file, "00000000"."00000001"."00000000"."00000000"."00000001"."00000001"."00000000"."00000000"."00000000"."00000000"."00000000");
								fwrite($file, $item["referencia"].$item["codigo_barras"]."            "."0000000".str_pad($item["precio1"], 10, " ", STR_PAD_LEFT));
								fwrite($file, str_pad($item["costo_actual"], 10, " ", STR_PAD_LEFT).$item["monto_exento"].str_pad($item["iva"], 5, " ", STR_PAD_LEFT));
								fwrite($file, "0000000".(($item["estatus"]=="A")?1:0)."0000000000".$item["fecha_creacion"].$dateReg2."            ".str_pad($item["costo_anterior"], 10, " ", STR_PAD_LEFT));
								fwrite($file, str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT).str_pad(0.00, 10, " ", STR_PAD_LEFT));
								fwrite($file, $dateReg2.str_pad($item["precio_viejo"], 10, " ", STR_PAD_LEFT)."\n\r");
							
						}
						

						fwrite($file, "3".$dateReg.str_pad($num, 10, "0", STR_PAD_LEFT)."\n\r");
				        fclose($file);
		            	
		            }
	            }


	    		$arrayAlmacenes = explode(',',$_POST['arrayAlmacenes']);
	    		for($i = 0; $i < count($arrayAlmacenes);$i++){
		        	$ruta = $path ."txt/rebajas/".$arrayAlmacenes[$i]."/" ;
					if(!file_exists($ruta)){
						mkdir ($ruta);
					}
					copy($archivo_actualizacion, $ruta.$archivo);
		        }


				$query = 'update rebaja r set r.status_txt=1,r.fecha_generacion_txt=now() where r.status_txt=0 ';
				if($request['rango'] == "fecha"){
					$request['fecha_ini'] = substr($request['fecha_ini'], 6, 4)."-".substr($request['fecha_ini'], 3, 2)."-".substr($request['fecha_ini'], 0, 2);
	        		$request['fecha_fin'] = substr($request['fecha_fin'], 6, 4)."-".substr($request['fecha_fin'], 3, 2)."-".substr($request['fecha_fin'], 0, 2);
					$query .= " and r.fecha_creacion between '".$request['fecha_ini']." 00:00:00' and '".$request['fecha_fin'] ." 23:59:59' ";
				}
				$this->bdControlador->setQuery($query);
				$this->bdControlador->ejecutaInstruccion();
				$this->bdControlador->commit();
				$this->bdControlador->desconectar();;
			}	
		    
			
            if($num == 0){
            	$mensaje = "No hay novedades pendientes";
            }
            else{

            	$mensaje = "Se genero el archivo TXT de ".$num." novedad(es)";
            }
			return  Array('success' => true,'mensaje' =>$mensaje ,'novedades' => $num);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getListTxtRebajasAlmacen($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);

			$archivo = "00000001.TXT";
			$ext_permitidas = array("TXT","txt");
		    
		    $matches = Array();
		    $archivoArray = Array();
		    $ext = "";
		    $iArchivos = 0;
		    $path = "../../../selectraerp/txt/rebajas/".$request['bodega']."/";
		    $pathDownload = "/pyme/selectraerp/txt/rebajas/".$request['bodega']."/";
		    //chmod($path, 777);
		    if (is_dir($path)){
			    $directorio = opendir($path); //ruta actual
		        $ext = "";
		        while ($archivo = readdir($directorio)){
		            if (!is_dir($archivo)){
		                $extAux = explode(".", $archivo);
		                if(count($extAux) != 1){
		                    $ext = strtolower($extAux[1]);
		                    if(in_array($ext,$ext_permitidas)){
				                $arrayTabla['txt'] = $archivo;
				                $arrayTabla['fecha'] = date ("d/m/Y H:i:s.", filemtime($path.$archivo));
				                $arrayTabla['ruta'] = $path.$archivo;
		                        $matches[] = $arrayTabla;
		                    }    
		                }            
		            }
		        }
		    }

			return  Array('success' => true,'mensaje' =>'OK' ,'matches' =>$matches);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
}
?>