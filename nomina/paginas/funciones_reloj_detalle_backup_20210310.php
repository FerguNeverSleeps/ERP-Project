<?php
//error_reporting(E_ALL);
//ini_set("display_errors","On");

function data_ordernar($a,$b){
  if($a["ficha"]>$b["ficha"]) return 1;
  if($a["ficha"]<$b["ficha"]) return -1;
  if($a["fecha"]>$b["fecha"]) return 1;
  if($a["fecha"]<$b["fecha"]) return -1;
  if($a["hora"] >$b["hora"])  return 1;
  if($a["hora"] <$b["hora"])  return -1;
  return 0;
}

class DBController{
  public $Connection;
  public $Result;
  public $MsgError;
  public function Connect($server="", $name="", $user="", $password="", $port=""){
    $this->Connection = mysqli_connect($server.($port?":".$port:""), $user, $password, $name) or die('Could not connect to server.' );
    $this->Execute("SET NAMES 'UTF8'");
  }
  public function Execute($sql){
    $this->MsgError="";
    ini_set("display_errors","Off");
    try{    
      $this->Result=mysqli_query($this->Connection, $sql);
      if(!$this->Result)
        throw new Exception("No se pudo realizar la consulta.");      
    }
    catch( Exception $e ){
      $this->MsgError=mysqli_error($this->Connection);
      return NULL;
    }
    if($this->Result===true) return true;
    else if($this->Result===false) return false;
    $return=array();
    while($row = mysqli_fetch_array($this->Result,MYSQL_ASSOC))
      $return[]=$row;
    ini_set("display_errors","On");
    return $return;
  }
  public function Insert($tabla,$columnaValor){
    $col='(';
    $val='(';     
    while(list($clave,$valor)=each($columnaValor)){
      $col.=$clave.', ';
      $val.=$valor.", ";
    }     
    $col=substr($col,0,strlen($col)-2);
    $val=substr($val,0,strlen($val)-2);
    $col.=')';
    $val.=')';
    $sql="INSERT INTO $tabla $col VALUES $val";
    return $this->Execute($sql)===NULL?FALSE:TRUE;
  }
  public function Update($tabla, $columnaValor, $condicion){   
    $cadena="";
    while(list($clave,$valor)=each($columnaValor))    
      $cadena.=$clave."=".$valor.", ";      
    $cadena=substr($cadena,0,strlen($cadena)-2);
    $sql="UPDATE $tabla SET $cadena WHERE $condicion";
    return $this->Execute($sql)===NULL?FALSE:TRUE;
  }
}

$db=new DBController();

$db->Connect(DB_HOST,DB_SELECTRA_NOM,DB_USUARIO,DB_CLAVE,DB_PUERTO);

//BUSCAR EN NOMEMPRESA valida_turno y rango_valida_turno
global $valida_turno;
$valida_turno=$db->Execute("select valida_turno, rango_valida_turno, horas_fin from nomempresa");
if(!isset($valida_turno[0]))
  $valida_turno=["valida_turno"=>"1","rango_valida_turno"=>"15", "horas_fin"=>"0"];
else
  $valida_turno=$valida_turno[0];


function reloj_detalle__posicion_hora($arreglo_horas){
  //Eliminar horas duplicadas
  $arreglo_horas=array_unique($arreglo_horas);

  //Ordenar el arreglo de horas de menor a mayor
  sort($arreglo_horas);

  //Preparar el arreglo con las posibles horas a cargar
  $data=[
    "entrada"=>"''",
    "salida"=>"''",
    "salmuerzo"=>"''",
    "ealmuerzo"=>"''",
    "ent_emer"=>"''",
    "sal_emer"=>"''"
  ];

  //Según sea el caso, de la cantidad de horas encontradas colocarlo en el campo de hora correspondiente
  switch(count($arreglo_horas)){
    case 1: //si encuentra solo 1 hora, significa que es entrada
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      break;      
    case 2: //si encuentra 2 horas, significa que es entrada y salida
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      $data["salida"]    ="'".$arreglo_horas[1]."'";
      break; 
    case 3: //si encuentra 3 horas, significa que es entrada, salida y entrada de almuerzo
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      $data["salmuerzo"] ="'".$arreglo_horas[1]."'";
      $data["ealmuerzo"] ="'".$arreglo_horas[2]."'";
      break; 
    case 4: //si encuentra 4 horas, significa que es entrada, salida y entrada de almuerzo
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      $data["salmuerzo"] ="'".$arreglo_horas[1]."'";
      $data["ealmuerzo"] ="'".$arreglo_horas[2]."'";
      $data["salida"]    ="'".$arreglo_horas[3]."'";
      break;
    case 5:
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      $data["salmuerzo"] ="'".$arreglo_horas[1]."'";
      $data["ealmuerzo"] ="'".$arreglo_horas[2]."'";
      $data["salida"]    ="'".$arreglo_horas[3]."'";
      $data["ent_emer"]  ="'".$arreglo_horas[4]."'";
      break;
    case 6:
    default:
      $data["entrada"]   ="'".$arreglo_horas[0]."'";
      $data["salmuerzo"] ="'".$arreglo_horas[1]."'";
      $data["ealmuerzo"] ="'".$arreglo_horas[2]."'";
      $data["salida"]    ="'".$arreglo_horas[3]."'";
      $data["ent_emer"]  ="'".$arreglo_horas[4]."'";
      $data["sal_emer"]  ="'".$arreglo_horas[5]."'";
      break; 
  }
  return $data;
}


function reloj_detalle__registrar($ficha,$fecha,$hora,$dispositivo_id,$encabezado_id=NULL){
  global $db, $ficha_database; 
  if(isset($ficha_database[$ficha]))
    $db->Connect(DB_HOST,$ficha_database[$ficha],DB_USUARIO,DB_CLAVE,DB_PUERTO);
  else{
    //para este caso, continuar con el proceso, que tome la bd por defecto, caso de ejecucion de la bd actualmente seleccionada desde el sistema web
  // return ["success"=>false,"message"=>"No se definio la base de datos de la ficha."];
  }
  
  //buscar el tipnom personal_id en nompersonal
  $personal=$db->Execute("select personal_id, tipnom FROM nompersonal WHERE ficha=".$ficha);
  if(!isset($personal[0]["personal_id"])) 
    return ["success"=>false,"message"=>"No existe la ficha $ficha."];

  //si no se especifica el encabezado, buscarlo por la fecha
  if($encabezado_id===NULL){
    //buscar el encabezado segun la fecha, tipo de nomina de la ficha.
    $encabezado_id=$db->Execute("
      SELECT cod_enca as id
      FROM reloj_encabezado 
      WHERE 
        tipo_nomina=".$personal[0]["tipnom"]."  AND 
        '".$fecha."' BETWEEN fecha_ini AND fecha_fin
    ");  
    if(!isset($encabezado_id[0]["id"])) 
      return ["success"=>false,"message"=>"No existe el encabezado para el tipo de nomina en la fecha $fecha."];
    $encabezado_id=$encabezado_id[0]["id"];
  }
  
  //Crear el arreglo para las horas
  $arreglo_horas=[];
  //si recibe como parametro un arreglo de horas, dejarlo tal cual, en caso contrario crear un arreglo
  if(is_array($hora))
    $arreglo_horas=$hora;
  else
    $arreglo_horas[]=$hora;

  //Almacenar en reloj_marcaciones
  $sql_delete_values="";
  $sql_insert_values="";
  for($i=0;$i<count($arreglo_horas);$i++){
    $sql_delete_values.="'".$arreglo_horas[$i]."',";
    $sql_insert_values.="('".$personal[0]["personal_id"]."','$ficha','$fecha','".$arreglo_horas[$i]."','$dispositivo_id','0','0'),";
  }
  $sql_delete_values=trim($sql_delete_values,",");
  $sql_insert_values=trim($sql_insert_values,",");
  //borrar registro previo e insertar registro nuevo en reloj_marcaciones
  $db->Execute("DELETE FROM reloj_marcaciones WHERE ficha_empleado='$ficha' and fecha='$fecha' and hora in ($sql_delete_values) and dispositivo='$dispositivo_id'");
  $db->Execute("INSERT INTO reloj_marcaciones (id_empleado,ficha_empleado,fecha,hora,dispositivo,tipo,estatus) VALUES $sql_insert_values");

  //buscar si existe en reloj_detalle encabezado_id, ficha, fecha y dispositivo_id
  $resultado=$db->Execute("
    SELECT id, entrada, salida, salmuerzo, ealmuerzo, ent_emer, sal_emer, estatus
    FROM reloj_detalle
    WHERE 
      id_encabezado = '$encabezado_id' AND 
      ficha = $ficha AND
      fecha = '$fecha' AND
      marcacion_disp_id = '$dispositivo_id'
  ");

  
  $data=[
    "id_encabezado"=>"'$encabezado_id'",
    "marcacion_disp_id"=>"'$dispositivo_id'",
    "ficha"=>"'$ficha'",
    "fecha"=>"'$fecha'"
  ];

  if(!isset($resultado[0]["id"])){//si no existe, insertar en reloj_detalle con valores cero y el campo hora colocarlo en entrada
    //Segun las horas armas los campos de entrada, salida, almuerzo ....
    $data=array_merge($data,reloj_detalle__posicion_hora($arreglo_horas));
    //Ingresar status=0, al insertarlo la primera vez
    $data["estatus"]=0;
    //Calcular las horas, para ingresarlas al insertar
    $calculo=reloj_detalle__calcular(NULL,$data,true);
    //Eliminar el campo success, acum_semanal del retorno (y cualquier otro que no este en la tabla reloj_detalle)
    unset($calculo["success"]);
    unset($calculo["message"]);
    unset($calculo["acum_semanal"]);
    //Para cada indice de $calculo, agregarlo en $data para luego insertarlo en la BD
    //print "\nCALCULO: ";print_r($calculo);
    foreach ($calculo as $indice => $valor) $data[$indice]="'$valor'";
    //si no hay hora de entrada, ni de salida no hacer insert.
    if((!trim($data["entrada"],"'") or substr(trim($data["entrada"],"'"), 0,5)=="00:00") and (!trim($data["salida"],"'") or substr(trim($data["salida"],"'"), 0,5)=="00:00"))
      return ["success"=>false,"message"=>"Registro sin horas que ingresar [Ficha: $ficha | Fecha: $fecha]."];

    //Buscar si la persona es capataz para activar el campo capataz en reloj_detalle
    $capataz=$db->Execute("SELECT nap.valor FROM nomcampos_adic_personal as nap, nomcampos_adicionales as na WHERE nap.id=na.id and upper(descrip) like '%CAPATAZ%' AND upper(nap.valor) LIKE '%SI%' and nap.ficha='$ficha'");    
    $data["capataz"]=isset($capataz[0]["valor"])?"'1'":"'0'";
    //Insertar data
    //print " DATA INSERTAR: ";print_r($data);
    $db->Insert("reloj_detalle",$data);
    //buscar el id del registro recien insertado
    $resultado=$db->Execute("SELECT LAST_INSERT_ID() as id");
    if(!isset($resultado[0]["id"]))
      return ["success"=>false,"message"=>"No pudo registrar en reloj_detalle."];

    $reloj_detalle_id=$resultado[0]["id"];
  }
  else{//si existe, verificar que campo estan cargados (entrada, salmuerzo, ealmuerzo, salida, ent_emer, sal_emer) y agregar la hora en el sitio correspondiente según lo que encuentre
    //solo permitir el registro cuando sea desde el connect estatus =0 (si tienen estatus = 1, 2 o 3) (dia nacional, asignar horas, edicion manual no insertar horas pq fueron ediciones manuales)
    if($resultado[0]["estatus"]!="0")
      return ["success"=>false,"message"=>"La ficha $ficha, fecha $fecha y dispositivo $dispositivo_id fue editada manualmente."];

    $reloj_detalle_id=$resultado[0]["id"];
    //Arear un arreglo con todas las horas encontradas en la BD
    $arreglo_horas_previas=[];
    if($resultado[0]["entrada"])    $arreglo_horas_previas[]=$resultado[0]["entrada"];
    if($resultado[0]["salida"])     $arreglo_horas_previas[]=$resultado[0]["salida"];
    if($resultado[0]["salmuerzo"])  $arreglo_horas_previas[]=$resultado[0]["salmuerzo"];
    if($resultado[0]["ealmuerzo"])  $arreglo_horas_previas[]=$resultado[0]["ealmuerzo"];
    if($resultado[0]["ent_emer"])   $arreglo_horas_previas[]=$resultado[0]["ent_emer"];
    if($resultado[0]["sal_emer"])   $arreglo_horas_previas[]=$resultado[0]["sal_emer"];
    //AUnir las horas nuevas con las anteriores
    $arreglo_horas=array_merge($arreglo_horas,$arreglo_horas_previas);
    //Segun las horas armas los campos de entrada, salida, almuerzo ....
    $data=array_merge($data,reloj_detalle__posicion_hora($arreglo_horas));
    //Calcular las horas, para ingresarlas al insertar
    $calculo=reloj_detalle__calcular(NULL,$data,true);
    //Eliminar el campo success del retorno
    unset($calculo["success"]);
    unset($calculo["message"]);
    unset($calculo["acum_semanal"]);
    //Para cada indice de $calculo, agregarlo en $data para luego insertarlo en la BD
    foreach ($calculo as $indice => $valor) $data[$indice]="'$valor'";    
    //actualizar relog_detalle para los casos encontrados
    $db->Update("reloj_detalle",$data,"id='$reloj_detalle_id'");
  }
  return ["success"=>"true", "reloj_detalle_id"=>$reloj_detalle_id];
}

//VERSION ORIGINAL AL 2021-03-10
function reloj_detalle__calcular($reloj_detalle_id,$data=NULL,$actualizar_salida_diasiguiente=false){
//function reloj_detalle__calcular($reloj_detalle_id,$data=NULL,$actualizar_salida_diasiguiente=false){
  global $db;
  //si tiene $data se usar para calcular los valores en caliente desde la vista
  if($data===NULL){
    $resultado=$db->Execute("SELECT *, id_encabezado as cod_enca FROM reloj_detalle WHERE id='$reloj_detalle_id'");

    if(!isset($resultado[0]["id"]))
      return ["success"=>false,"message"=>"No obtener información del reloj_detalle con id=".$resultado[0]["id"]];    
  }
  else{
    //$dispositivo_id=$db->Execute("SELECT marcacion_disp_id FROM reloj_detalle WHERE id='".."'");

    $resultado=[$data];

  }

  if($resultado[0]["salida_diasiguiente"]!="SI")
    $resultado[0]["salida_diasiguiente"]="";

  $id              = trim($resultado[0]["id"],"'");
  $cod_enca        = trim($resultado[0]["cod_enca"],"'");
  $dispositivo_id  = trim($resultado[0]["marcacion_disp_id"],"'");
  $ent             = trim($resultado[0]["entrada"],"'");
  $salm            = trim($resultado[0]["salmuerzo"],"'");
  $ealm            = trim($resultado[0]["ealmuerzo"],"'");
  $sal             = trim($resultado[0]["salida"],"'");
  $salidaDS        = trim($resultado[0]["salida_diasiguiente"],"'");
  $entradaEmer     = trim($resultado[0]["ent_emer"],"'");
  $salidaEmer      = trim($resultado[0]["sal_emer"],"'");
  $fecha           = trim($resultado[0]["fecha"],"'");
  $ficha           = trim($resultado[0]["ficha"],"'");
  
  $tiempo       = new tiempo();

  global $valida_turno;
  if($valida_turno["valida_turno"]=="2"){
    $valida_turno_tolerancia=60*$valida_turno["rango_valida_turno"];
    $sql="SELECT tur.* FROM nomturnos tur where TIME_TO_SEC('$ent') >= (TIME_TO_SEC(entrada)-$valida_turno_tolerancia) and  TIME_TO_SEC('$ent')<=(TIME_TO_SEC(entrada)+$valida_turno_tolerancia)";
    $fila=$db->Execute($sql);
  }
  else {
    $fila=$db->Execute("select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'");    
  }

  //print "select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='".trim($ficha,"'")."' and per.fecha='".trim($fecha,"'")."'";
  //print_r($fila);
  if(!isset($fila[0])) return ["success"=>false,"message"=>"Turno no encontrado para la ficha $ficha ($fecha)."];
  $fila=$fila[0];

  $turno_id=$fila["turno_id"];



  //verificar si el turno es dia siguiente
  if($actualizar_salida_diasiguiente){
    //buscar dia anterior y el turno su turno
    $fecha_anterior  = new DateTime($fecha);
    $fecha_anterior->modify("-1 days");
    $registro_anterior=$db->Execute("select * from reloj_detalle where marcacion_disp_id='$dispositivo_id' and ficha='$ficha' and fecha='".$fecha_anterior->format("Y-m-d")."'");
    if(isset($registro_anterior[0]["salida_diasiguiente"]) and $registro_anterior[0]["salida_diasiguiente"]!="SI")
      $registro_anterior[0]["salida_diasiguiente"]="";

    //burcar turno del dia anterior
    $turno_dia_anterior_sw=false;
    if(isset($registro_anterior[0]["turno"]) and $registro_anterior[0]["turno"]){
      $turno_dia_anterior=$db->Execute("select * from nomturnos where turno_id='".$registro_anterior[0]["turno"]."'");
      if(isset($turno_dia_anterior[0])){
        if(substr($turno_dia_anterior[0]["salida"],0,5)<substr($turno_dia_anterior[0]["entrada"],0,5)){
          $turno_dia_anterior_sw=true;
        }
      }  


    }

    //En el turno -> si la hora de salida es menor a la entrada
    if((substr($fila["salida"],0,5)<substr($fila["entrada"],0,5) and $salidaDS!="SI") || $turno_dia_anterior_sw ){ 
      //CASO 1
      //buscar dia anterior
      /*$fecha_anterior  = new DateTime($fecha);
      $fecha_anterior->modify("-1 days");
      $registro_anterior=$db->Execute("select * from reloj_detalle where marcacion_disp_id='$dispositivo_id' and ficha='$ficha' and fecha='".$fecha_anterior->format("Y-m-d")."'");
      if(isset($registro_anterior[0]["salida_diasiguiente"]) and $registro_anterior[0]["salida_diasiguiente"]!="SI")
        $registro_anterior[0]["salida_diasiguiente"]="";
*/
      //print "<br>DIA ANTERIOR: ";
      //print_r($registro_anterior);

      //si hay dia anterior 
      if(isset($registro_anterior[0]) and $registro_anterior[0]["salida_diasiguiente"]!="SI"){
        $registro_anterior=$registro_anterior[0];
        //si dia anterior, tiene solo entrada (no tiene salida) y en fecha_anterior[entrada] < fecha[hora_salida], 
        //mover fecha[hora entrada] a fecha_anterior[hora salida]
        $registro_anterior["entrada"]= substr($registro_anterior["entrada"],0,5);
        $registro_anterior["salida"] = substr($registro_anterior["salida"],0,5);
        //si en la fecha hay entrada y no hay salida
        //$time_ent       = $tiempo->aminutos($registro_anterior["entrada"]);
        $time_ent       = $tiempo->aminutos($ent);
        $time_ent_turno = $tiempo->aminutos(substr($fila["entrada"],0,5));
        $time_sal_turno = $tiempo->aminutos(substr($fila["salida"],0,5));
        $tolerancia_turno_dia_siguiente=180;


        if($registro_anterior["entrada"] and $registro_anterior["entrada"]!="00:00" and 
         (!$registro_anterior["salida"]  or  $registro_anterior["salida"] =="00:00")){
          //verificar si la entrada del dia anterior corresponde a la salida del turno (dentro de la tolerancia) y la entrada actual es menor a la tolerancia del turno. Puede significar que $ent es la salida del dia anterior

          /*if($registro_anterior["entrada"]>=substr($fila["tolerancia_llegada"],0,5) and 
             substr($ent,0,5) <substr($fila["tolerancia_llegada"],0,5)){*/
            //insertar hora en anterior $ent como salida y borrar $ent en el registro actual y mover los registros hacia arriba (horas)
            $db->Update("reloj_detalle",["salida"=>"'$ent'","salida_diasiguiente"=>"'SI'"],"id='".$registro_anterior["id"]."'");
            //actualizar horas en dia anterior
            reloj_detalle__actualizar($registro_anterior["id"]);
            //si queda vacio (entrada='' and salida='')
            //borrar registro
            if($id){
              $db->Update("reloj_detalle",["entrada"=>"salida","salida"=>"''","salida_diasiguiente"=>"''"],"id='$id'");
              return reloj_detalle__actualizar($id);              
            }
            else{
              $entrada_nueva  =$data['salida'];
              $data['entrada']=$data['salida'];
              $data['salida'] ="";
              $data['salida_diasiguiente']="";    
              $tmp=reloj_detalle__calcular(NULL,$data,true);
              $tmp['entrada']=trim($entrada_nueva,"'");
              $tmp['salida'] ="";
              $tmp['salida_diasiguiente']="";  
              return $tmp;
            }
          //}
        }       
        //si tiene entrada y salida
        else if($registro_anterior["entrada"] and $registro_anterior["entrada"]!="00:00" and 
                $registro_anterior["salida"]  and $registro_anterior["salida"] !="00:00"){ 
          //si el dia anterior no tiene salida
          //print "<br><br>FICHA: $ficha - TIENE ENTRADA=".$registro_anterior["entrada"]."   SALIDA=".$registro_anterior["salida"];
        }
      }
      else {

        
        
      }
      
      //CASO 2
      //buscar el dia siguiente
      /*$fecha_siguiente  = new DateTime($fecha);
      $fecha_siguiente->modify("+1 days");
      $registro_siguiente=$db->Execute("select * from reloj_detalle where marcacion_disp_id='$dispositivo_id' and ficha='$ficha' and fecha='".$fecha_siguiente->format("Y-m-d")."'");

      if(isset($registro_siguiente[0]) and !trim($registro_anterior[0]["salida_diasiguiente"])){
        $registro_siguiente=$registro_siguiente[0];      
        $registro_siguiente["entrada"]= substr($registro_siguiente["entrada"],0,5);
        $registro_siguiente["salida"] = substr($registro_siguiente["salida"],0,5);
        //si el siguiente dia tiene entrada y no tiene salida
        //ó si tiene entrada y tiene salida y la entrada es menor a la salida
        if(($registro_siguiente["entrada"] and $registro_siguiente["entrada"]!="00:00" and (!$registro_siguiente["salida"]  or  $registro_siguiente["salida"] =="00:00")) or
            $registro_siguiente["entrada"] and $registro_siguiente["salida"] and $registro_siguiente["entrada"]<$registro_siguiente["salida"]){
          //traer horas del dia sig al dia actual y mover las horas del dia sig hacia arriba

          //mover hora de salida a hora de entrada en dia siguiente
          $db->Update("reloj_detalle",["entrada"=>"salida","salida"=>"''"],"id='".$registro_siguiente["id"]."'");
          //actualizar horas en el dia siguiente
          reloj_detalle__actualizar($registro_siguiente["id"]);

          $db->Update("reloj_detalle",["salida"=>"'$ent'","salida_diasiguiente"=>"'SI'"],"id='".$registro_anterior["id"]."'");



        }

      }*/

      //si tiene entrada y salida, y si la entrada < salida
      if($resultado[0]["entrada"] and $resultado[0]["entrada"]!="00:00" and 
         $resultado[0]["salida"]  and $resultado[0]["salida"] !="00:00" and 
         substr($resultado[0]["entrada"],0,5)<substr($resultado[0]["salida"],0,5) and 
         substr($fila["salida"],0,5)<substr($fila["entrada"],0,5) //turno con salida al dia siguiente
       ){
        //invertir horas (entrada/salida) y activa DS
        //si es un registro ya guardado
        if($id){
          $db->Update("reloj_detalle",["entrada"=>"'".$resultado[0]['salida']."'","salida"=>"'".$resultado[0]['entrada']."'","salida_diasiguiente"=>"'SI'"],"id='$id'");
          return reloj_detalle__actualizar($id); 
        }
        else{
          $entrada_nueva  =$data[0]['salida'];
          $data['entrada']=$data[0]['salida'];
          $data['salida'] =$entrada_nueva;
          $data['salida_diasiguiente']="SI";    
          $tmp=reloj_detalle__calcular(NULL,$data,true);
          return $tmp;
        }
      }
      else if($resultado[0]["entrada"] and $resultado[0]["entrada"]!="00:00" and 
         $resultado[0]["salida"]  and $resultado[0]["salida"] !="00:00" and 
         substr($resultado[0]["entrada"],0,5)<substr($resultado[0]["salida"],0,5) and 
         substr($fila["salida"],0,5)>substr($fila["entrada"],0,5) and //turno normal sin DS,
         $salidaDS=="SI" //tiene marcado el campo dia siguiente
       ){//Si es turno normal (sin dia siguiente y tiene la marca de DS, quitarle el SI al registro y recalcular)

        if($id){
          $db->Update("reloj_detalle",["salida_diasiguiente"=>"''"],"id='$id'");
          return reloj_detalle__actualizar($id); 
        }
        else{
          $data['salida_diasiguiente']="";    
          $tmp=reloj_detalle__calcular(NULL,$data,true);
          return $tmp;
        }
        //print "<bR> entro2 $fecha  ".$resultado[0]["entrada"] . " / ". $resultado[0]["salida"] ;
      }
    }




  }//fin if($actualizar_salida_diasiguiente)
  



  $turno        = trim($fila["descripcion"]);
  $libre        = $fila["libre"];
  $tipo         = $fila["tipo"];
  $descpago     = $fila["descpago"];

  //print $libre;

  $entrada0     = $tiempo->aminutos(substr($fila["tolerancia_llegada"], 0,5));
  $entrada1     = $tiempo->aminutos(substr($fila["entrada"], 0,5));
  $entradatol   = $tiempo->aminutos(substr($fila["tolerancia_entrada"], 0,5));

  $saldesc1     = $tiempo->aminutos(substr($fila["inicio_descanso"],0,2)=="00" ? "24".substr($fila["inicio_descanso"], 2,5) : substr($fila["inicio_descanso"], 0,5));
  $entdesc1     = $tiempo->aminutos(substr($fila["salida_descanso"], 0,5));
  $entdesctol   = $tiempo->aminutos(substr($fila["tolerancia_descanso"],0,2)=="00" ? "24".substr($fila["tolerancia_descanso"], 2,5) : substr($fila["tolerancia_descanso"], 0,5));
  $salida1      = $tiempo->aminutos(substr($fila["salida"],0,2)=="00" ? "24".substr($fila["salida"], 2,5) : substr($fila["salida"], 0,5));
  $salidatol    = $tiempo->aminutos(substr($fila["tolerancia_salida"],0,2)=="00" ? "24".substr($fila["tolerancia_salida"], 2,5) : substr($fila["tolerancia_salida"], 0,5));

  $descanso     = $entdesc1 - $saldesc1;
  $lim1         = $saldesc1 - $entrada1;
  $lim2         = $salida1 - $entdesc1;

  $lim          = $lim1 + $lim2;

  $entrada      = $salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
  $entradaf     = $salmuf=$ealmuf=$salidaf="";
  $tardanza     = $extra=$domingo="";
  
  $extra        = $regular=$regular1=$regular2=$regular3=$regular4="";
  $extra        = $extra1=$extra2=$extra3=$extra4="";
  $extraNoc     = $extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
  $extraExt     = $extraExt1=$extraExt2=$extraExt3=$extraExt4="";
  $extraNocExt  = $extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
  
  $hExt         = $tiempo->aminutos("03:00");
  $limiteExt    = $tiempo->aminutos("18:00");
  $limiteExtFin = $tiempo->aminutos("06:00");
  $limiteExtNoc = $tiempo->aminutos("24:00");   
  $limite       = $lim;  
  
 
     
  if($ent!=""){
    $ent = substr($ent, 0,4)=="24" ? "00".substr($ent, 2,5) : substr($ent, 0,5);
    $entrada = $tiempo->aminutos($ent);
  } 


  if($salm!=""){
    $salmu = $tiempo->aminutos($salm);
  }
  //else
  //  $salmu = $tiempo->aminutos($saldesc1);

  if($ealm!=""){
    $ealmu = $tiempo->aminutos($ealm);
  }
  //else
  //  $ealmu = $tiempo->aminutos($entdesc1);



  if($sal!=""){
    $salidaPDS = $tiempo->aminutos(substr($sal, 0,5));
    $sal       = substr($sal, 0,2)=="00" ? "24".substr($sal, 2,5) : substr($sal, 0,5);
    $salida    = $tiempo->aminutos($sal);
  }
  if($entradaEmer!=""){
    $entradaEmerf = $entradaEmer;
    $entradaEmer  = $tiempo->aminutos($entradaEmer);
  }
  if($salidaEmer!=""){
    $salidaEmerf = $salidaEmer;
    $salidaEmer  = $tiempo->aminutos($salidaEmer);
  }
  if($salidaDS!=""){
    $salidaDSf = $salidaDS;
    $salidaDS  = $tiempo->aminutos($salidaDS);
  }



  $extra11   =$extra=$regular=$regular1=$regular2=$regular3=$extrah="";
  $tardanza1 =$tardanza2=$tardanza3=$tardanza4="";
  $extrah1   =$extrah2=$extrah3=$extrah4="";
  $regular1  = $regular2 = 0;
  $band      = 0;

  //caso cuando se inserta por primera vez desde el connect
  if($ent!='' and ($sal=='' or $sal=='00:00' or $sal=='24:00')){
    if($entrada > $entradatol){
        $tardanza = "".$entrada-$entrada1;
    }
  }

  if(($entrada != "") && ($salmu != ""))
  {
    if($entrada < $entrada0)
    {
      if($entrada < $limiteExtFin)
      {
        $extraNoc1 = $limiteExtFin-$entrada;
        $extra1 = $entrada1-$limiteExtFin;
      }
      else
      {
        $extra1 = $entrada1-$entrada;
      }
      $regular1 = $salmu - $entrada1;
    }
    elseif($entrada > $entradatol)
    {
      $tardanza1 = $entrada-$entrada1;
      $regular1 = $salmu - $entrada;
    }
    elseif(($entrada >= $entrada0) && ($entrada <= $entradatol))
    {
      $regular1 = $salmu - $entrada1;
    }
    

    //PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
    if($libre == 0)
    {
      if($salmu < $saldesc1)
      {
        $tardanza3 = $salmu - $saldesc1;
      }
      elseif(($salmu > $saldesc1) && ($salmu <= $entdesc1))
      {
        //$extrah1 = $saldesc1->diff($salmu);
        $extrah1 = $saldesc1-$salmu;
      }
    }
    $band=1;      
  }
  if(($ealmu != "") && ($salida != ""))
  {
    //$regular2 = $ealmu - $salida;

    //PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
    if($libre == 0)
    {
      if($ealmu > $entdesctol)
      {
        $tardanza4 = $ealmu - $entdesc1;

      }
      elseif($ealmu <= $entdesc1)
      {
        $extrah2 = $entdesc1 - $ealmu;
        ////////AQUI
        //$regular2 = $salida - $entdesc1;
        $ealmu = $entdesc1;
      }
      elseif($salmu > $entdesc1)
      {
        $extrah4 = $salmu - $ealmu;
      }
    }

    if($salida > $salidatol)
    {
      if($salida > $limiteExt)
      {
        /*if($salida1<$limiteExt)
        {
          $extra2 = $salida1->diff($limiteExt);
          $extraNoc2 = $limiteExt->diff($salida);
          $regular = $limite;
        }
        else
        {
          $extraNoc2 = $salida1->diff($salida);
          $regular = $limite; 
        }*/
        //$extraNoc2 = $salida - $salida1;
        //$salida = $limiteExt;
        if($salida1<=$limiteExt)
        {
          $extraNoc2 = $salida - $limiteExt;
          $extra2 = $limiteExt - $salida1;
        }
        else
        {
          $extraNoc2 = $salida - $salida1;
        }
        $salida = $salida1;
      }
      else
      {
        $extra2 = $salida - $salida1;
        $salida = $salida1;
      }
      //elseif(($salida<=$limiteExt)&&($regular>$limite))
      /*elseif($salida<=$limiteExt)
      {
        $extra2 = $salida1->diff($salida);
        $regular = $limite;
      }*/
    }
    elseif(($salida >= $salida1) && ($salida <= $salidatol))
    {
      $salida = $salida1;
    }
    elseif($salida < $salida1)
    {
      $tardanza2 = $salida1 - $salida;
    }
    
    $regular2 = $salida - $ealmu;

    if(($regular1>0)&&($regular2>0))
      $regular = $regular1 + $regular2;
    elseif($regular1>0)
      $regular = $regular1;
    elseif($regular2>0)
      $regular = $regular2;

    ///////////////////

    if(($extra1 != "") && ($extra2 != ""))
      $extra = $extra1 + $extra2;
    elseif($extra1 != "")
      $extra = $extra1;
    elseif($extra2 != "")
      $extra = $extra2;

    if(($extraNoc1 != "") && ($extraNoc2 != ""))
      $extraNoc = $extraNoc2 + $extraNoc1;
    elseif($extraNoc1 != "")
      $extraNoc = $extraNoc1;
    elseif($extraNoc2 != "")
      $extraNoc = $extraNoc2;
    $band=1;
  }

  if(($entrada !== "") && ($salida !== "") && ($band == 0))
  {
    //echo $limiteExtFin;
    if($entrada<=$entrada0)
    {
      if($entrada < $limiteExtFin)
      {
        $extraNoc1 = $limiteExtFin-$entrada;
        //$extra1 = $entrada1-$limiteExtFin;
      }
      else
      {
        $extra1 = $entrada1-$entrada;
      }
      $entrada = $entrada1;
    }
    elseif($entrada > $entradatol)
    {
      $tardanza1 = $entrada - $entrada1;
      //$regular=$entrada->diff($salida);
    }
    elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
    {
      $entrada = $entrada1;
    }
    //$regular = new DateTime($regular->format('%H:%I'));
    //$regular = new DateTime($regular->format('H:i'));
    //$regular = new DateTime($regular->format('%H:%I'));
    if($salida > $salidatol)
    {
      if($salida > $limiteExt && $salidatol < $limiteExt)
      {
        $extra2 = $limiteExt - $salida1;
        $extraNoc2 = $salida - $limiteExt;
        $salida = $salida1;
      }
      elseif($salida > $limiteExt)
      {
        $extraNoc2 = $salida - $salida1;
        $salida = $salida1;
        /*if($salida1<$limiteExt)
        {
          $extra2 = $salida1->diff($limiteExt);
          $extraNoc2 = $limiteExt->diff($salida);
          $regular = $limite;
        }
        else
        {
          $extraNoc2 = $salida1->diff($salida);
          $regular = $limite;
        }*/
      }
      elseif($salida <= $limiteExt)
      {
        $extra2 = $salida - $salida1;
        $salida = $salida1;
      }
    }
    elseif($salida < $salida1)
    {
      $tardanza2 = $salida1 - $salida;

      /*if($nocturno == 1)
      {
        if($entrada<$entrada1)
          $entradaxxx=$entrada1;
        $lim1x = $saldesc1->diff($entradaxxx);
        $lim2x = $salida->diff($entdesc1);

        $auxreg = explode(":",$lim2x->format('%H:%I'));
        $auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
        $limxx = new DateTime($lim1x->format('%H:%I'));
        $limxxx = $limxx->add(new DateInterval($auxreg1));
        $regular = $limxxx;
        if($regular>=$limite)
          $regular=$limite;
      }*/
    }
    if($salida <= $salidatol && $salida >= $salida1)
    {
      $salida = $salida1;
    }



    $regular = $salida - $entrada;
    //print "\n\nENTRADA: $entrada       SALIDA: $salida      DIFF: $regular          ".$tiempo->ahoras($regular);
    if($descpago==0 && $regular > 480)
    {
      /*$auxregx = 0;
      $auxregx = $regular - 480;
      if($auxregx <= $descanso)
        $regular -= $auxregx;
      else
        $regular -= $descanso;
      */
      $regular = $limite;
    }


    //$regular -= $descanso;


    
    if(($extra1 != "") && ($extra2 != ""))
      $extra = $extra1 + $extra2;
    elseif($extra1 != "")
      $extra = $extra1;
    elseif($extra2 != "")
      $extra = $extra2;



    if($salidaDS)
    {
      $regular += ($salidaDS - $salidaPDS);
    }

    $porcion_descanzo_tomada=0;
    if($entrada and $salida and !$ealmu and !$ealmu){
      //$saldesc1
      //entdesc1

      //if($salida>$saldesc1)

      //print "\n".$saldesc1."   ".$entdesc1."   ".($saldesc1-$entdesc1);
      $regular = $salida - $entrada;
      $regular -= $descanso;
    }


    //print "\nENTRADA: $entrada       SALIDA: $salida      DIFF: $regular          ".$tiempo->ahoras($regular-$porcion_descanzo_tomada)."     salidaDS:".$salidaDS."      salidaPDS: $salidaPDS     limite: $limite";
    /*if($salm=="" and $ealm=="" and $sal>$fila["salida_descanso"]){
      
    }*/


    if($regular>$limite)
    {
      $extra += ($regular - $limite);
      $regular=$limite;
    }

    if(($extraNoc1 != "") && ($extraNoc2 != ""))
      $extraNoc = $extraNoc1 + $extraNoc2;
    elseif($extraNoc1!="")
      $extraNoc = $extraNoc1;
    elseif($extraNoc2!="")
      $extraNoc = $extraNoc2;

    /*if($regular==$limite)
    {
      $extrah1 = $saldesc1->diff($entdesc1);
    }*/
  }

  if(($libre == 1) && ($salmu != "") && ($ealmu != ""))
  {
    $talmutol = $entdesctol - $saldesc1;
    
    $talmu =  $entdesc1 -$saldesc1;

    $talmur = $ealmu - $salmu;

    if($talmur>$talmutol)
    {
      $tardanza3 = $talmur - $talmu;
    }
    elseif($talmur<=$talmu)
    {
      $extrah1 = $talmu - $talmur;
    }
  }

  if(($extrah1 != "") && ($extrah2 != ""))
  {
    $extrah = $extrah1 + $extrah2;
  }
  elseif($extrah1 != "")
    $extrah = $extrah1;
  elseif($extrah2 != "")
    $extrah = $extrah2;

  if(($regular == "") && ($regular1 != ""))
    $regular = $regular1;

  //if($extraNoc)
  //  $extraNoc = $extraNoc;
  
  if($extra > $hExt)
  {
    $extraExt = $extra - $hExt;
    $extra = $hExt;
  }

  if($extraNoc>$hExt)
  {
    $extraNocExt = $extraNoc - $hExt;
    $extraNoc = $hExt;
  }

  //echo "1".$tardanza1;
  //echo "2".$tardanza2;
  //echo "++3".$tardanza3;
  //echo "++4".$tardanza4;
  if(($tardanza1 != "") && ($tardanza2 != ""))
    $tardanza = $tardanza1 + $tardanza2;
  elseif($tardanza1)
    $tardanza = $tardanza1;
  elseif($tardanza2)
    $tardanza = $tardanza2;

  if($tardanza3)
  {
    if($tardanza)
      $tardanza += $tardanza3;
    else
      $tardanza = $tardanza3;
  }

  if($tardanza4)
  {
    if($tardanza)
      $tardanza += $tardanza4;
    else
      $tardanza = $tardanza4;
  }
  
  if($regular!="")
  {
    //420 son 07:00
    if(($limite==420)&&($regular>=$limite)&&($salida>=$salida1))
    {
      $regular=480;
    }
    if($regular>=$limite)
    {
      $regular=$limite;
    }
  }

  $emergencia = $descansoincompleto = 0;
  if(($entradaEmer != "") && ($salidaEmer != ""))
  {
    $emergencia = $salidaEmer - $entradaEmer;
    if($emergencia >= 180 )
    {
      $regular += $emergencia;
      $descansoincompleto = $entradaEmer - $salida;
      $emergencia = 0;
    }
  }

  if($regular <= 0)
    $regular = "";

  if($extra <= 0)
    $extra = "";
  

  //si la suma de horas extras y horas extras noc > 3horas, la diferencia pasa a ser extranoc
  //if($tiempo->aminutos("sal")>$tiempo->aminutos("18:00"))
  if($extra+$extraNoc > 180){
    $diferencia=$extra+$extraNoc-180;
    $extraNoc-=$diferencia;
    $extraNocExt=$extraNoc2-$extraNoc;
  }

  //si es sabado, solo tomar en cuenta extra y extraext. no van extranoc y extranocexet (van como extrext)
  if(date("w",strtotime($fecha))==6 and $valida_turno["horas_fin"]=="1"){ 
    //print "es sabado: $extraNoc, $extraNocExt";
    $extraExt+=$extraNoc+$extraNocExt;
    $extraNoc=0;
    $extraNocExt=0;
  }


  if($tipo==6)
  { 
    $dialibre=$regular;
    $regular=""; 
  }
 
  if(date("w",strtotime($fecha))==0)
  { 
    $domingo=$regular;
    $regular="";
    
  
    if($tipo==3)
    {
      $extraMixDiurna=$extra;
      $extraExtMixDiurna=$extraExt;
      $extraMixNoc=$extraNoc;
      $extraExtMixNoc=$extraExtNoc;

      $extra = "";
      $extraNoc = "";
      $extraExt="";
      $extraNocExt="";
    }
    elseif($tipo==4)
    {
      $extraMixNoc=$extra;
      $extraExtMixNoc=$extraExt;
      $extraMixNoc=$extraNoc;
      $extraExtMixNoc=$extraExtNoc;      

      $extra = "";
      $extraNoc = "";
      $extraExt="";
      $extraNocExt="";
    }

  }
  /*else {
    if($tipo==3)
    {
      $extraMixDiurna=$extra;
      $extraExtMixDiurna=$extraExt;
      

      $extra = "";
      $extraNoc = "";
      $extraExt="";
      $extraNocExt="";
    }
    elseif($tipo==4)
    {
      $extraMixNoc=$extra;
      $extraExtMixNoc=$extraExt;
      

      $extra = "";
      $extraNoc = "";
      $extraExt="";
      $extraNocExt="";
    }
  }*/




  $sql = "select fecha_ini, fecha_fin, tipo_nomina from reloj_encabezado where cod_enca='$cod_enca'";
  $reloj_encabezado=$db->Execute($sql);

  $resultado_dia_fiesta=$db->Execute("select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha' and cod_tiponomina = '".$reloj_encabezado[0]["tipo_nomina"]."'");
  if(isset($resultado_dia_fiesta[0]))
    if($resultado_dia_fiesta[0]["dia_fiesta"]==3){
      $nacional=$regular;
      $regular="";   
      /*no hace falta, si se deja descuadra, ya se aplica arriba si es domingo o dia se semana luego de este segmento
      if($tipo==3){
        $extraMixDiurna=$extra;
        $extraExtMixDiurna=$extraExt;
        $extraMixNoc=$extraNoc;
        $extraExtMixNoc=$extraExtNoc;

        $extra = "";
        $extraNoc = "";
        $extraExt="";
        $extraNocExt="";
      }
      */
    }
   

  if(($tipo==3)&&(date("w",strtotime($fecha))!=0)){
    $extraMixDiurna=$extra;
    $extraExtMixDiurna=$extraExt;
    $extraMixNoc=$extraNoc;
    $extraExtMixNoc=$extraNocExt;

    $extra = "";
    $extraNoc = "";
    $extraExt="";
    $extraNocExt="";
  }
  //print "tipo: $tipo, ";
  if(($tipo==4)&&(date("w",strtotime($fecha))!=0)){
    $extraMixNoc=$extra;
    $extraExtMixNoc=$extraExt;
    $extraMixNoc=$extraNoc;
    $extraExtMixNoc=$extraNocExt;

    $extra = "";
    $extraNoc = "";
    $extraExt="";
    $extraNocExt="";
  }


  

  //calculo de horas regulares cuando dia siguiente tiene valor.
  //los calculos se hacen en base a $salida, silida_diasiguiente es solo una bandera para indicar que la $salida tiene la hora de salida del siguiente dia.
  //estoy colocando valor SI,
  //colocar en la interfaz de edición un select con valor <select id='salida_diasiguiente'><option value='SI'>SI</option value=''>NO<option></option></select>
  //con el codigo anterior el calculo de horas extras y tradanza los realiza bien.
  if(strtoupper(trim($resultado[0]["salida_diasiguiente"]))=="SI"){
    $regular=24*60+$salida-$entrada;
    if(!$salida or !$entrada) $regular="";
  }

  //SEGMENTO PARA EL CALCULO DE LAS 9HORAS SEMANALES
  //Funcioa para bisemanas y quincenas
  //buscar hacia atras
  $time_actual=strtotime($fecha);
  $time_fecha_ini=strtotime($reloj_encabezado[0]["fecha_ini"]);
  $time_parte1=strtotime("+6 day",$time_fecha_ini);


  //si forma parte de la porción 1
  $add="";
  if($time_actual>=$time_fecha_ini and $time_actual<=$time_parte1){
    $add="rd.fecha BETWEEN '".$reloj_encabezado[0]["fecha_ini"]."' and '$fecha' and";
  }
  else {//se le suma 1 dia a la fecha tope para la 2da porcion (+24*3600)    
    $add="rd.fecha BETWEEN '".date("Y-m-d",$time_parte1+24*3600)."' and '$fecha' and";    
  }
  $sql="select 
      sum(TIME_TO_SEC(rd.extra))/60 as extra,
      sum(TIME_TO_SEC(rd.extraext))/60 as extraext,
      sum(TIME_TO_SEC(rd.extranoc))/60 as extranoc,
      sum(TIME_TO_SEC(rd.extraextnoc))/60 as extraextnoc
    from 
      reloj_detalle as rd
    where 
      $add
      rd.id<>'$id' and
      rd.ficha='$ficha' and
      rd.id_encabezado='$cod_enca' and 
      rd.marcacion_disp_id='$dispositivo_id'
    ";
  $horas_extras=$db->Execute($sql);
  //print_r($horas_extras);
  //print_r($sql);

  $acumulado_horas=0;
  if(isset($horas_extras[0]))
    $acumulado_horas=$horas_extras[0]["extra"]+$horas_extras[0]["extraext"]+$horas_extras[0]["extranoc"]+$horas_extras[0]["extraextnoc"];  
  //si se excede de las 9horas, pasar horas extras a extendidas   540=9*60
  if($extra+$extraExt+$extraNoc+$extraNocExt>0)
  if($extra+$extraExt+$extraNoc+$extraNocExt+$acumulado_horas > 9*60){//9horas semanales    
    //si el acumulado de la semanana + las horas extras excede las 9horas, restar la diferencia a las horas extras + esa dif sumarla a las extras extendidas
    if($extra>0){
      if($acumulado_horas>9*60){
        $extraExt=$extra;
        $extra=0;
      }
      else if($acumulado_horas+$extra>9*60){
        $tmp=$acumulado_horas+$extra-9*60;
        $extra=$extra-$tmp;
        $extraExt+=$tmp;
      }
    }

    //lo mismo pero para las nocturnas
    if($extraNoc>0){
      if($acumulado_horas>9*60){
        $extraNocExt=$extraNoc;
        $extraNoc=0;
      }
      else if($acumulado_horas+$extraNoc>9*60){
        $tmp=$acumulado_horas+$extraNoc-9*60;
        $extraNoc=$extraNoc-$tmp;
        $extraNocExt+=$tmp;
      }
    }
  }
  //FIN SEGMENTO PARA EL CALCULO DE LAS 9HORAS SEMANALES
  //print "EXTRA:".$tiempo->ahoras($extra)."   EXTRA EXT:".$tiempo->ahoras($extraExt);

  
  return [
    "success"            => true,
    "ordinaria"          => $tiempo->ahoras($regular),
    "extra"              => $tiempo->ahoras($extra),
    "domingo"            => $tiempo->ahoras($domingo),
    "tardanza"           => $tiempo->ahoras($tardanza),
    "extraext"           => $tiempo->ahoras($extraExt),
    "extranoc"           => $tiempo->ahoras($extraNoc),
    "extraextnoc"        => $tiempo->ahoras($extraNocExt),
    "nacional"           => $tiempo->ahoras($nacional),
    "descextra1"         => $tiempo->ahoras($extrah),
    "mixtodiurna"        => $tiempo->ahoras($extraMixDiurna),
    "mixtoextdiurna"     => $tiempo->ahoras($extraExtMixDiurna),
    "mixtonoc"           => $tiempo->ahoras($extraMixNoc),
    "mixtoextnoc"        => $tiempo->ahoras($extraExtMixNoc),
    "emergencia"         => $tiempo->ahoras($emergencia),
    "descansoincompleto" => $tiempo->ahoras($descansoincompleto),
    "dialibre"           => $tiempo->ahoras($dialibre),
    "acum_semanal"       => $tiempo->ahoras($acumulado_horas),
    "turno"              => $turno_id
  ];
}




function reloj_detalle__actualizar($reloj_detalle_id,$actualizar_salida_diasiguiente=false){
  global $db;
  $resultado=reloj_detalle__calcular($reloj_detalle_id,NULL,$actualizar_salida_diasiguiente);
  if(!$resultado["success"]) return;
  $modificar=[
    "ordinaria"          => "'".$resultado["ordinaria"]."'",
    "extra"              => "'".$resultado["extra"]."'",
    "domingo"            => "'".$resultado["domingo"]."'",
    "tardanza"           => "'".$resultado["tardanza"]."'",
    "extraext"           => "'".$resultado["extraext"]."'",
    "extranoc"           => "'".$resultado["extranoc"]."'",
    "extraextnoc"        => "'".$resultado["extraextnoc"]."'",
    "nacional"           => "'".$resultado["nacional"]."'",
    "descextra1"         => "'".$resultado["descextra1"]."'",
    "mixtodiurna"        => "'".$resultado["mixtodiurna"]."'",
    "mixtoextdiurna"     => "'".$resultado["mixtoextdiurna"]."'",
    "mixtonoc"           => "'".$resultado["mixtonoc"]."'",
    "mixtoextnoc"        => "'".$resultado["mixtoextnoc"]."'",
    "emergencia"         => "'".$resultado["emergencia"]."'",
    "descansoincompleto" => "'".$resultado["descansoincompleto"]."'",
    "dialibre"           => "'".$resultado["dialibre"]."'",
    "turno"              => "'".$resultado["turno"]."'"
  ];

  $db->Update("reloj_detalle",$modificar,"id='$reloj_detalle_id'");
}

/*
funcion reloj_detalle__registrar_lote(data,encabezado_id,debug)
  Debe recibir un arreglo con los campoas (ficha,fecha,hora,dispositivo_id)

  1er Parametro: arreglo con la info a registrar de la forma [Ref. 1]
  2do Parametro (encabezado_id): para registrar en un encabezado específico, si es NULL registrar en el encabezado correspondiente segun la fecha.
  3er Parametro (debug): para imprimir en pantalla como va el procesamiento del txt
*/
function reloj_detalle__registrar_lote($data,$encabezado_id=NULL,$debug=false){  
  //Arreglo para guardar el retorno de reloj_detalle__registrar()
  $tmp=[];
  //para reducir el tiempo de ejecución agrupar las horas en un arreglo por ficha, fecha y dispositivo
  //para cada registro en $data
  $data_agrupada=[];
  for($i=0;$i<count($data);$i++){
    //buscar si existe en $data_agrupada (ficha,fecha,dispositivo_id)
    $encontro=false;
    for($j=0;$j<count($data_agrupada);$j++){ 
      if($data[$i]["ficha"]==$data_agrupada[$j]["ficha"] and $data[$i]["fecha"]==$data_agrupada[$j]["fecha"] and $data[$i]["dispositivo_id"]==$data_agrupada[$j]["dispositivo_id"]){
        //si existe, agregar la hora
        $data_agrupada[$j]["hora"][]=$data[$i]["hora"];
        $encontro=true;
        break;
      }
    }
    if(!$encontro)
      $data_agrupada[]=["ficha"=>$data[$i]["ficha"],"fecha"=>$data[$i]["fecha"],"dispositivo_id"=>$data[$i]["dispositivo_id"],"hora"=>[$data[$i]["hora"]]];    
  }
  $data=$data_agrupada;
  //para cada registro en $data
  for($i=0;$i<count($data);$i++){
    if($debug) print "\nAgreando en horas en reloj_detalle ".($i+1)."/".count($data);
    //Registrar reloj_detalle la ficha, fecha, hora y dispositivo    
    $resultado=reloj_detalle__registrar($data[$i]["ficha"],$data[$i]["fecha"],$data[$i]["hora"],$data[$i]["dispositivo_id"],$encabezado_id);
  }
}





?>