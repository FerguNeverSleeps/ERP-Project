<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'BDControlador.php');
class ModeloIntegrante{
	private $bdControlador = null;

 	public function fecha_sql($value) { // fecha de DD/MM/YYYY a YYYYY/MM/DD
	    return substr($value, 6, 4) . "-" . substr($value, 3, 2) . "-" . substr($value, 0, 2);
	}

	public function save($request,$files){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$query = '';
			if($request['personal_id'] == 0){
				$filesize = $files['foto']['size'];
		        $filetype = $files['foto']['type'];
		        $archivo = $files['foto']['name'];//HTTP_POST_FILES
		        if ($archivo != "") {
		            $nombre_archivo1 = $request['ficha']."_".$files['foto']['name'];//HTTP_POST_FILES
		            $tipo_archivo = $files['foto']['type'];//HTTP_POST_FILES
		            $tamano_archivo = $files['foto']['size'];//HTTP_POST_FILES
		            if (copy($files['foto']['tmp_name'], "../../../nomina/paginas/fotos/" . $nombre_archivo1)) {//move_uploaded_file($HTTP_POST_FILES['mifichero']['tmp_name'], nomina/paginas/fotos."/".$nombre_archivo1)){
		                chmod("../../../nomina/paginas/fotos/" . $nombre_archivo1, 0777);
		            } else {
		                throw new Exception("Error al subir archivo", 1);
		            }
		        }

				$filesize2 = $files['imagen_cedula']['size'];
		        $filetype2 = $files['imagen_cedula']['type'];
		        $archivo2  = $files['imagen_cedula']['name'];
		        $imagen_cedula = '';
		        if ($archivo2 != "") {
		            $nombre_archivo2  = $request['ficha']."_".$files['imagen_cedula']['name'];
		            $tipo_archivo2    = $files['imagen_cedula']['type'];
		            $tamano_archivo2  = $files['imagen_cedula']['size'];
		            if (copy($files['imagen_cedula']['tmp_name'], "../../../nomina/paginas/fotos/" . $nombre_archivo2)) {
		                chmod("../../../nomina/paginas/fotos/" . $nombre_archivo2, 0777);
		                $imagen_cedula = 'fotos/'.$nombre_archivo2;
		            } else {
		                throw new Exception("Error al subir archivo", 1);
		            }
		        }

		        if ($request[codnivel1] == '') {
		            $cod_nive1 = 0;
		        } else {
		            $cod_nive1 = $_POST[codnivel1];
		        }
		        if ($request[codnivel2] == '') {
		            $cod_nive2 = 0;
		        } else {
		            $cod_nive2 = $request[codnivel2];
		        }
		        if ($request[codnivel3] == '') {
		            $cod_nive3 = 0;
		        } else {
		            $cod_nive3 = $request[codnivel3];
		        }
		        if ($request[codnivel4] == '') {
		            $cod_nive4 = 0;
		            
		        } else {
		            $cod_nive4 = $request[codnivel4];
		        }
		        if ($request[codnivel5] == '') {
		            $cod_nive5 = 0;
		        } else {
		            $cod_nive5 = $request[codnivel5];
		        }
		        if ($request[codnivel6] == '') {
		            $cod_nive6 = 0;
		        } else {
		            $cod_nive6 = $request[codnivel6];
		        }
		        if ($request[codnivel7] == '') {
		            $cod_nive7 = 0;
		        } else {
		            $cod_nive7 = $request[codnivel7];
		        }
		        $s_monto = $request[sueldopro];
		        //$s_monto=str_replace (".", " ", $_POST[txtmonto]);
		        //$s_monto=str_replace (",", " ", $s_monto);
		        $temp1 = $request['fecnac'];
		        $temp2 = $request['fecing'];
		        $temp3 = $request['fecharetiro'];
		        $temp4 = $request['fecha_decreto'];
		        $temp5 = $request['fecha_decreto_baja'];
		        if ($temp1[4] != '-' && $temp1[7] != '-') {
		            $fecha = $this->fecha_sql($request['fecnac']);
		        } else {
		            $fecha = $request['fecnac'];
		        }
		        if ($temp2[4] != '-' && $temp2[7] != '-') {
		            $fecha1 = $this->fecha_sql($request['fecing']);
		        } else {
		            $fecha1 = $request['fecing'];
		        }

		        if ($temp3[4] != '-' && $temp3[7] != '-') {
		            $fecha2 = $this->fecha_sql($request['fecharetiro']);
		        } else {
		            $fecha2 = $request['fecharetiro'];
		        }
		        if ($temp4[4] != '-' && $temp4[7] != '-') {
		            $request['fecha_decreto'] = $this->fecha_sql($request['fecha_decreto']);
		        } else {
		            $request['fecha_decreto'] = $request['fecha_decreto'];
		        }
		        if ($temp5[4] != '-' && $temp5[7] != '-') {
		            $request['fecha_decreto_baja'] = $this->fecha_sql($request['fecha_decreto_baja']);
		        } else {
		            $request['fecha_decreto_baja'] = $request['fecha_decreto_baja'];
		        }

		      	/*$query="INSERT INTO nompersonal (
		                    foto,
		                    nacionalidad,
		                    cedula,
		                    apellidos,
		                    nombres,
		                    apenom,
		                    sexo,
		                    estado_civil,
		                    fecnac,
		                    lugarnac,
		                    codpro,
		                    direccion,
		                    telefonos,
		                    email,
		                    estado,
		                    fecing,
				    fecharetiro,
		                    ficha,
		                    tipopres,
		                    codbancob,
		                    cuentacob,
		                    codbanlph,
		                    cuentalph,tipemp,sueldopro,suesal,tipnom,codcat,codcargo,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,inicio_periodo,fin_periodo, turno_id,seguro_social,hora_base,segurosocial_sipe,dv) VALUES 
				('fotos/".$nombre_archivo1."',
				'".$request['optNacionalidad']."',
				'".$request['txtcedula']."',
				'".$request['txtapellidos']."',
				'".$request['txtnombres']."',
				'".$request['txtapellidos'].", ".$request['txtnombres']."',
				'".$request['optSexo']."',
				'".$request['cboEstadocivil']."',
				'".$fecha."',
				'".$request['txtlugarNac']."',
				'".$request['cboProfesion']."',
				'".$request['txtdireccion']."',
				'".$request['txttelefonos']."',
				'".$request['txtemail']."',
				'".$request['cbosituacion']."',
				'".$fecha1."',
				'".$fecha2."',
				'".$request['txtficha']."',
				'".$request['cboTipocobro']."',
				'".$request['cboBancos']."',
				'".$request['txtcuenta']."',
				'".$request['cbobancoaux']."',
				'".$request['txtcuentaaux']."',
				'".$request['optContrato']."',
				'".$s_monto."',
				'".$s_monto."',
				'".$request['cboTipoNomina']."',
				'".$request['cboCategorias']."',
				'".$request['cboCargos']."',
				'".$cod_nive1."',
				'".$cod_nive2."',
				'".$cod_nive3."',
				'".$cod_nive4."',
				'".$cod_nive5."',
				'".$cod_nive6."',
				'".$cod_nive7."',
				'".$this->fecha_sql($request['fecha_inicio_contrato'])."',
				'".$this->fecha_sql($request['fecha_fin_contrato'])."',
				".(($request["cboTurno"]=="")?"NULL":"'".$request["cboTurno"]."'").",
				'".$request['txtsegurosocial']."',
				'".$request['txthorabase']."',
				'".$request['txtsegurosocialsipe']."',
				'".$request['txtdv']."')";*/
//forcob
				$query="INSERT INTO nompersonal (
		                    foto,
		                    nomposicion_id,
		                    nacionalidad,
		                    cedula,
		                    apellidos,
		                    nombres,
		                    apenom,
		                    sexo,
		                    estado_civil,
		                    fecnac,
		                    lugarnac,
		                    codpro,
		                    direccion,
		                    telefonos,
		                    email,
		                    estado,
		                    fecing,
				    fecharetiro,
		                    ficha,
		                    tipopres,
		                    forcob,
		                    codbancob,
		                    cuentacob,
		                    codbanlph,
		                    cuentalph,tipemp,sueldopro,suesal,tipnom,codcat,codcargo,codnivel1,codnivel2,codnivel3,codnivel4,codnivel5,codnivel6,codnivel7,inicio_periodo,fin_periodo, turno_id,seguro_social,hora_base,segurosocial_sipe,dv,num_decreto,fecha_decreto,num_decreto_baja,fecha_decreto_baja,siacap, puesto_id, imagen_cedula
) VALUES 
				('fotos/".$nombre_archivo1."',
					'".$request['nomposicion_id']."',
				'".$request['nacionalidad']."',
				'".$request['cedula']."',
				'".$request['apellidos']."',
				'".$request['nombres']."',
				'".$request['apellidos'].", ".$request['nombres']."',
				'".$request['sexo']."',
				'".$request['estado_civil']."',
				'".$fecha."',
				'".$request['lugarnac']."',
				'".$request['codpro']."',
				'".$request['direccion']."',
				'".$request['telefonos']."',
				'".$request['email']."',
				'".$request['estado']."',
				'".$fecha1."',
				'".$fecha2."',
				'".$request['ficha']."',
				'".$request['tipopres']."',
				'".$request['forcob']."',
				'".$request['codbancob']."',
				'".$request['cuentacob']."',
				'".$request['codbanlph']."',
				'".$request['cuentalph']."',
				'".$request['tipemp']."',
				'".$s_monto."',
				'".$s_monto."',
				'".$request['tipnom']."',
				'".$request['codcat']."',
				'".$request['codcargo']."',
				'".$cod_nive1."',
				'".$cod_nive2."',
				'".$cod_nive3."',
				'".$cod_nive4."',
				'".$cod_nive5."',
				'".$cod_nive6."',
				'".$cod_nive7."',
				'".$this->fecha_sql($request['inicio_periodo'])."',
				'".$this->fecha_sql($request['fin_periodo'])."',
				".(($request["turno_id"]=="")?"NULL":"'".$request["turno_id"]."'").",
				'".$request['seguro_social']."',
				'".$request['hora_base']."',
				'".$request['segurosocial_sipe']."',
				'".$request['dv']."',
				'".$request['num_decreto']."',
				(case when '".$request['fecha_decreto']."'!='' then '".$request['fecha_decreto']."' else NULL end),
				'".$request['num_decreto_baja']."',
				(case when '".$request['fecha_decreto_baja']."'!='' then '".$request['fecha_decreto_baja']."' else NULL end),
				'".$request['siacap']."',
				NULLIF('". (isset($request['puesto_id']) ? $request['puesto_id'] : '')."',''),
				NULLIF('".$imagen_cedula."', '') )";
		        
		        $this->bdControlador->setQuery($query);
				$result = $this->bdControlador->ejecutaInstruccion();
			}
			else{
				$filesize = $files['foto']['size'];
		        $filetype = $files['foto']['type'];
		        $archivo = $files['foto']['name'];//HTTP_POST_FILES
		        $setFoto = "";
		        if ($archivo != "") {
		            $nombre_archivo1 = $request['ficha']."_".$files['foto']['name'];//HTTP_POST_FILES
		            $tipo_archivo = $files['foto']['type'];//HTTP_POST_FILES
		            $tamano_archivo = $files['foto']['size'];//HTTP_POST_FILES
		            if (copy($files['foto']['tmp_name'], "../../../nomina/paginas/fotos/" . $nombre_archivo1)) {//move_uploaded_file($HTTP_POST_FILES['mifichero']['tmp_name'], nomina/paginas/fotos."/".$nombre_archivo1)){
		                chmod("../../../nomina/paginas/fotos/" . $nombre_archivo1, 0777);
		                $setFoto = "foto='fotos/".$nombre_archivo1."',";
		            } else {
		                throw new Exception("Error al subir archivo", 1);
		            }		            
		        }
		        else{
					$setFoto = "";
		        }

				$filesize2 = $files['imagen_cedula']['size'];
		        $filetype2 = $files['imagen_cedula']['type'];
		        $archivo2  = $files['imagen_cedula']['name'];
		        $setFoto2  = "";
		        if ($archivo2 != "") 
		        {
		            $nombre_archivo2 = $request['ficha']."_".$files['imagen_cedula']['name'];
		            $tipo_archivo2   = $files['imagen_cedula']['type'];
		            $tamano_archivo2 = $files['imagen_cedula']['size'];
		            if (copy($files['imagen_cedula']['tmp_name'], "../../../nomina/paginas/fotos/" . $nombre_archivo2)) {
		                chmod("../../../nomina/paginas/fotos/" . $nombre_archivo2, 0777);
		                $setFoto2 = "imagen_cedula='fotos/".$nombre_archivo2."',";
		            } else {
		                throw new Exception("Error al subir archivo", 1);
		            }		            
		        }
		        else{
					$setFoto2 = "";
		        }

		        if ($request[codnivel1] == '') {
		            $cod_nive1 = 0;
		        } else {
		            $cod_nive1 = $_POST[codnivel1];
		        }
		        if ($request[codnivel2] == '') {
		            $cod_nive2 = 0;
		        } else {
		            $cod_nive2 = $request[codnivel2];
		        }
		        if ($request[codnivel3] == '') {
		            $cod_nive3 = 0;
		        } else {
		            $cod_nive3 = $request[codnivel3];
		        }
		        if ($request[codnivel4] == '') {
		            $cod_nive4 = 0;
		            
		        } else {
		            $cod_nive4 = $request[codnivel4];
		        }
		        if ($request[codnivel5] == '') {
		            $cod_nive5 = 0;
		        } else {
		            $cod_nive5 = $request[codnivel5];
		        }
		        if ($request[codnivel6] == '') {
		            $cod_nive6 = 0;
		        } else {
		            $cod_nive6 = $request[codnivel6];
		        }
		        if ($request[codnivel7] == '') {
		            $cod_nive7 = 0;
		        } else {
		            $cod_nive7 = $request[codnivel7];
		        }
		        $s_monto = $request[sueldopro];
		        //$s_monto=str_replace (".", " ", $_POST[txtmonto]);
		        //$s_monto=str_replace (",", " ", $s_monto);
		        $temp1 = $request['fecnac'];
		        $temp2 = $request['fecing'];
		        $temp3 = $request['fecharetiro'];
		        $temp4 = $request['fecha_decreto'];
		        $temp5 = $request['fecha_decreto_baja'];
		        if ($temp1[4] != '-' && $temp1[7] != '-') {
		            $fecha = $this->fecha_sql($request['fecnac']);
		        } else {
		            $fecha = $request['fecnac'];
		        }
		        if ($temp2[4] != '-' && $temp2[7] != '-') {
		            $fecha1 = $this->fecha_sql($request['fecing']);
		        } else {
		            $fecha1 = $request['fecing'];
		        }

		        if ($temp3[4] != '-' && $temp3[7] != '-') {
		            $fecha2 = $this->fecha_sql($request['fecharetiro']);
		        } else {
		            $fecha2 = $request['fecharetiro'];
		        }
		        if ($temp4[4] != '-' && $temp4[7] != '-') {
		            $request['fecha_decreto'] = $this->fecha_sql($request['fecha_decreto']);
		        } else {
		            $request['fecha_decreto'] = $request['fecha_decreto'];
		        }
		        if ($temp5[4] != '-' && $temp5[7] != '-') {
		            $request['fecha_decreto_baja'] = $this->fecha_sql($request['fecha_decreto_baja']);
		        } else {
		            $request['fecha_decreto_baja'] = $request['fecha_decreto_baja'];
		        }
				$query="update nompersonal set 
		                    ".$setFoto."
		                    ".$setFoto2."
		                    nacionalidad='".$request['nacionalidad']."',
		                    nomposicion_id='".$request['nomposicion_id']."',
		                    cedula='".$request['cedula']."',
		                    apellidos='".$request['apellidos']."',
		                    nombres='".$request['nombres']."',
		                    apenom='".$request['apellidos'].", ".$request['nombres']."',
		                    sexo='".$request['sexo']."',
		                    estado_civil='".$request['estado_civil']."',
		                    fecnac='".$fecha."',
		                    lugarnac='".$request['lugarnac']."',
		                    codpro='".$request['codpro']."',
		                    direccion='".$request['direccion']."',
		                    telefonos='".$request['telefonos']."',
		                    email='".$request['email']."',
		                    estado='".$request['estado']."',
		                    fecing='".$fecha1."',
				    fecharetiro='".$fecha2."',
		                    ficha='".$request['ficha']."',
		                    tipopres='".$request['tipopres']."',
		                    forcob='".$request['forcob']."',
		                    codbancob='".$request['codbancob']."',
		                    cuentacob='".$request['cuentacob']."',
		                    codbanlph='".$request['codbanlph']."',
		                    cuentalph='".$request['cuentalph']."',
		                    tipemp='".$request['tipemp']."',
		                    sueldopro='".$s_monto."',
		                    suesal='".$s_monto."',
		                    tipnom='".$request['tipnom']."',
		                    codcat='".$request['codcat']."',
		                    codcargo='".$request['codcargo']."',
		                    codnivel1='".$cod_nive1."',
		                    codnivel2='".$cod_nive2."',
		                    codnivel3='".$cod_nive3."',
		                    codnivel4='".$cod_nive4."',
		                    codnivel5='".$cod_nive5."',
		                    codnivel6='".$cod_nive6."',
		                    codnivel7='".$cod_nive7."',
		                    inicio_periodo='".$this->fecha_sql($request['inicio_periodo'])."',
		                    fin_periodo='".$this->fecha_sql($request['fin_periodo'])."', 
		                    turno_id=".(($request["turno_id"]=="")?"NULL":"'".$request["turno_id"]."'").",
		                    seguro_social='".$request['seguro_social']."',
		                    hora_base='".$request['hora_base']."',
		                    segurosocial_sipe='".$request['segurosocial_sipe']."',
		                    dv= '".$request['dv']."' ,
		                    num_decreto= '".$request['num_decreto']."' ,
		                    fecha_decreto= (case when '".$request['fecha_decreto']."'!='' then '".$request['fecha_decreto']."' else NULL end) ,
		                    num_decreto_baja= '".$request['num_decreto_baja']."' ,
		                    fecha_decreto_baja= (case when '".$request['fecha_decreto_baja']."'!='' then '".$request['fecha_decreto_baja']."' else NULL end) ,
		                    siacap= '".$request['siacap']."',
		                    puesto_id = NULLIF('". (isset($request['puesto_id']) ? $request['puesto_id'] : '')."','') 
		                    where personal_id = '".$request['personal_id']."'";
		        
		        $this->bdControlador->setQuery($query);
				$result = $this->bdControlador->ejecutaInstruccion();

				$this->modelo = new ModeloLog_transacciones();
				if($request["sueldopro"] != $request["sueldo_original"]){
					$request["descripcion"] = 'Modificacion de sueldo a ficha '.$request['ficha'].'. Sueldo anterior '.$request["sueldo_original"];
					$request["modulo"] = 'Datos-Integrantes';
					$request["url"] = 'ModeloMaestro.php';
					$request["accion"] = 'editar';
					$request["valor"] = $request["sueldopro"]; 
					$request["op"] = "add";
					$response = $this->modelo->save($request);
				}
				if($request["codcargo"] != $request["cargo_original"]){
					$request["descripcion"] = 'Modificacion de cargo a ficha '.$request['ficha'].'. Cargo anterior '.$request["cargo_original"];
					$request["modulo"] = 'Datos-Integrantes';
					$request["url"] = 'ModeloMaestro.php';
					$request["accion"] = 'editar';
					$request["valor"] = $request["codcargo"] ; 
					$request["op"] = "add";
					$response = $this->modelo->save($request);
				}
				if($request["nomposicion_id"] != $request["posicion_original"]){
					$request["descripcion"] = 'Modificacion de posicion a ficha '.$request['ficha'].'. Posicion anterior '.$request["posicion_original"];
					$request["modulo"] = 'Datos-Integrantes';
					$request["url"] = 'ModeloMaestro.php';
					$request["accion"] = 'editar';
					$request["valor"] = $request["nomposicion_id"]; 
					$request["op"] = "add";
					$response = $this->modelo->save($request);
				}
			}
			

			$this->bdControlador->commit();
			$this->bdControlador->desconectar();;
			return  Array('success' => true,'mensaje' => "Proceso Finalizado");
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}

	public function getIntegrante($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nompersonal where 1";
			$query .= " and (ficha='".$this->bdControlador->real_escape_string($request['ficha'])."') ";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<integrante>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</integrante></message>';

			$this->bdControlador->desconectar();;
			return  $xml;//Array('success' => true,'data' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}

	public function getList($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';

			$bucarPor = $request['buscar_por'];
			$bucarPorEstado = $request['buscar_por_estado'];

			/*if(isset($request['muestra_egresados']) && $request['muestra_egresados'] == 1){
				//$where = " and s.situacion like '%Egresado%' ";
				$where = " and i.estado like '%Egresado%' ";
			}
			else{
				//$where = " and s.situacion not like '%Egresado%' ";
				$where = " and i.estado not like '%Egresado%' ";
			}*/
			$where = " and i.estado like '%".$bucarPorEstado."%' ";

			if($request['texto_buscar'] != ''){
				$texto = $this->bdControlador->real_escape_string($request['texto_buscar']);
			}
			
			if($bucarPor == -1 || $bucarPor == 'Todo' || $bucarPor == ''){
				$buscar = " and (i.cedula like '%".$texto."%' or i.nombres like '%".$texto."%' or i.apellidos like '%".$texto."%' or i.ficha like '%".$texto."%') ";
			}
			elseif($bucarPor == 0){
				$buscar = " and (i.nombres like '%".$texto."%' or i.apellidos like '%".$texto."%') ";
			}
			elseif($bucarPor == 1){
				$buscar = " and (i.cedula like '%".$texto."%') ";
			}
			elseif($bucarPor == 2){
				$buscar = " and (p.descripcion_posicion like '%".$texto."%') ";
			}

			$query = "SELECT i.cedula,i.estado,i.nombres, i.apellidos, i.ficha,i.foto,s.situacion estado_des,i.descrip ,i.apenom
			    FROM nomvis_integrantes i 
			    left join nomsituaciones s on s.codigo = i.estado 
			    left join nomposicion p on p.nomposicion_id = i.nomposicion_id 
			    WHERE i.descrip = '".$_SESSION['nomina']."' ".$where." ";
			if($request['texto_buscar'] != ''){
				$query .= $buscar;
			}

			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);

			$query = "SELECT i.cedula,i.estado,i.nombres, i.apellidos, i.ficha,i.foto,s.situacion estado_des,i.descrip ,i.apenom
			    FROM nomvis_integrantes i 
			    left join nomsituaciones s on s.codigo = i.estado 
			    left join nomposicion p on p.nomposicion_id = i.nomposicion_id 
			    WHERE i.descrip = '".$_SESSION['nomina']."' ".$where." ";
			if($request['texto_buscar'] != ''){
				$query .= $buscar;
			}
			$query .= " limit ".$request['start'].",".$request['limit'];
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();

			$matches = Array();
			while ($item = $this->bdControlador->fetch($result)){
				//$item["apelidosynombres"] = $item[apellidos].' , '.$item[nombres];
				if(strlen($item['foto'])>=7)            
		        {$item["foto"] = $item[foto];}
		        else
		        {$item["foto"]='';}
				$matches[] = $item;
			}
			$this->bdControlador->desconectar();;
			return  Array('success' => true,'totalCount' => $num,'matches' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	
	public function getPlanillaHorizontal($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';

			$bucarPor = $request['buscar_por'];
			$bucarPorEstado = $request['buscar_por_estado'];

			/*if(isset($request['muestra_egresados']) && $request['muestra_egresados'] == 1){
				//$where = " and s.situacion like '%Egresado%' ";
				$where = " and i.estado like '%Egresado%' ";
			}
			else{
				//$where = " and s.situacion not like '%Egresado%' ";
				$where = " and i.estado not like '%Egresado%' ";
			}*/
			$where = " and i.estado like '%".$bucarPorEstado."%' ";

			if($request['texto_buscar'] != ''){
				$texto = $this->bdControlador->real_escape_string($request['texto_buscar']);
			}
			
			if($bucarPor == -1 || $bucarPor == 'Todo' || $bucarPor == ''){
				$buscar = " and (i.cedula like '%".$texto."%' or i.nombres like '%".$texto."%' or i.apellidos like '%".$texto."%' or i.ficha like '%".$texto."%') ";
			}
			elseif($bucarPor == 0){
				$buscar = " and (i.nombres like '%".$texto."%' or i.apellidos like '%".$texto."%') ";
			}
			elseif($bucarPor == 1){
				$buscar = " and (i.cedula like '%".$texto."%') ";
			}
			elseif($bucarPor == 2){
				$buscar = " and (p.descripcion_posicion like '%".$texto."%') ";
			}

			/*$query = "SELECT i.cedula,i.estado,i.nombres, i.apellidos, i.ficha,i.foto,s.situacion estado_des,i.descrip ,i.apenom
			    FROM nomvis_integrantes i 
			    left join nomsituaciones s on s.codigo = i.estado 
			    left join nomposicion p on p.nomposicion_id = i.nomposicion_id 
			    WHERE i.descrip = '".$_SESSION['nomina']."' ".$where." ";
			if($request['texto_buscar'] != ''){
				$query .= $buscar;
			}

			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			*/
			$query = "SELECT nn.descrip departamento,i.cedula,i.estado,i.nombres, i.apellidos, i.ficha,i.foto,s.situacion estado_des,i.descrip ,i.apenom
			    FROM nomvis_integrantes i 
			    INNER JOIN nompersonal np on np.personal_id = i.personal_id 
			    INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1 
			    left join nomsituaciones s on s.codigo = i.estado 
			    left join nomposicion p on p.nomposicion_id = i.nomposicion_id 
			    WHERE i.descrip = '".$_SESSION['nomina']."' ".$where." ";
			if($request['texto_buscar'] != ''){
				$query .= $buscar;
			}
			//$query .= " limit ".$request['start'].",".$request['limit'];
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();

			$matches = Array();
			while ($item = $this->bdControlador->fetch($result)){
				//$item["apelidosynombres"] = $item[apellidos].' , '.$item[nombres];
				if(strlen($item['foto'])>=7)            
		        {$item["foto"] = $item[foto];}
		        else
		        {$item["foto"]='';}
				$matches[] = $item;
			}
			$this->bdControlador->desconectar();;
			return  Array('success' => true,'totalCount' => $num,'matches' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
}
?>