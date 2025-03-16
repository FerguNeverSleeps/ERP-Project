<?php
$nomina_id = $_GET['nomina'];
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

function limpia_espacios($cadena)
{
  $resultado = str_replace(" ", "0", $cadena);
  return "0".$resultado;
}
//----------------------------------
$sql2="SELECT * FROM parametro_inclusion";
$result2 = $db->query($sql2,$conexion);
$entidad = mysqli_fetch_array($result2);
//----------------------------------
$sql2="SELECT b.partida FROM nom_movimientos_nomina AS a,nomtipos_nomina AS b WHERE a.codnom = '$nomina_id' AND a.tipnom = b.tipnom";
$result2 = $db->query($sql2,$conexion);
$res = mysqli_fetch_array($result2);
$partida = explode(".", $res['partida']);
//----------------------------------
if(isset($partida[0]))
$area=$partida[0];
//----------------------------------
if(isset($partida[1]))
$institucion=$partida[1];
//----------------------------------
if(isset($partida[2]))
$tipo_presupuesto=$partida[2];
//----------------------------------
if(isset($partida[3]))
$programa=$partida[3];
//----------------------------------
if(isset($partida[4]))
$fuente=$partida[4];
//----------------------------------
if(isset($partida[5]))
$sub_prog=$partida[5];
//----------------------------------
if(isset($partida[7]))
$actividad=$partida[6];
//----------------------------------
if(isset($partida[7]))
$objeto=$partida[7];
//----------------------------------
if (isset($nomina_id))
{
      //Para generar el archivo a descargar BANCO.zip
      $archivo = "PLANILLA_ADICIONAL.ZIP";
      $zip = new ZipArchive();

      if ($zip->open($archivo, ZipArchive::CREATE) !== TRUE) {
      exit("No se puede abrir <$filename>");
      }

      $planilla = "PLANILLA.TXT";
      $partida = "PARTIDA.TXT";
      $descuento = "DESCUENTO.TXT";
      $audito = "AUDITO.TXT";
      // Para generar PLANILLA.TXT dentro de BANCO.zip
      $sqlplanilla = "SELECT b.ficha,b.nombres,b.apellidos,b.cedula,b.segurosocial_sipe,b.seguro_social,b.nomposicion_id,b.codnivel1,b.codnivel2 FROM nom_movimientos_nomina AS a
        LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
        WHERE a.codnom = '$nomina_id'
        GROUP BY b.ficha ORDER BY b.nomposicion_id";
      $res_sql_planilla = $db->query($sqlplanilla,$conexion);

      $file = fopen($planilla, "w");
      while($dato = fetch_array($res_sql_planilla))
      {
        $sql3 = "SELECT SUM(a.monto) AS sueldo FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.ficha = '".$dato['ficha']."' AND a.codcon=100";
        $res3 = $db->query($sql3,$conexion);
        $sueldo= mysqli_fetch_array($res3);
        //----------------------------------
        $sql4 = "SELECT SUM(a.monto) AS isr FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.ficha = '".$dato['ficha']."' AND a.codcon=202";
        $res3 = $db->query($sql4,$conexion);
        $res3= mysqli_fetch_array($res3);
        if ($res3['isr'] == 0.00) {
          $isr=$res3['isr']."E";
        }else{
          $isr=$res3['isr']."M";
        }
        //----------------------------------
        $sql4 = "SELECT SUM(a.monto) AS iss FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.ficha = '".$dato['ficha']."' AND a.codcon=202";
        $res3 = $db->query($sql4,$conexion);
        $res3= mysqli_fetch_array($res3);
        if ($res3['iss'] == 0.00) {
          $iss=$res3['iss']."E";
        }else{
          $iss=$res3['iss']."M";
        }
        //----------------------------------
        $sql5 = "SELECT SUM(a.monto) AS ise FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.ficha = '".$dato['ficha']."' AND a.codcon=202";
        $res3 = $db->query($sql5,$conexion);
        $res3= mysqli_fetch_array($res3);
        if ($res3['ise'] == 0.00) {
          $ise=$res3['ise']."E";
        }else{
          $ise=$res3['ise']."M";
        }
        //----------------------------------
        fwrite($file,
          limpia_espacios(
          $dato['segurosocial_sipe']).
          "0".$entidad['area'].
          "20230".
          $dato['nomposicion_id'].
          $dato['nombres']."\t\t".
          $dato['apellidos']."\t".
          $dato['seguro_social'].
          "0".$dato['codnivel1'].
          "00000".$sueldo['sueldo'].
          "00000".$isr.
          "00000".$iss.
          "00000".$ise.
          "A00000".
          PHP_EOL);
      }
      fclose($file);
      $zip->addFile($planilla);

      // Para generar DESCUENTO.TXT dentro de BANCO.zip
      $sqlplanilla = "SELECT b.ficha,b.nombres,b.apellidos,b.cedula,b.segurosocial_sipe,b.seguro_social,b.nomposicion_id,b.codnivel1,b.codnivel2 FROM nom_movimientos_nomina AS a
        LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
        WHERE a.codnom = '$nomina_id'
        GROUP BY b.ficha ORDER BY b.nomposicion_id";
      $res_sql_planilla = $db->query($sqlplanilla,$conexion);

      $file = fopen($descuento, "w");
      while($dato = fetch_array($res_sql_planilla))
      {
        $ficha = $dato['ficha'];
        //----------------------------------
        $sql5 = "SELECT SUM(a.monto) AS descuentos FROM nom_movimientos_nomina AS a WHERE a.ficha = '$ficha' AND a.tipcon='D' AND a.codnom = '$nomina_id' AND a.codcon NOT IN (100,200,201,202,204)";
        $res3 = $db->query($sql5,$conexion);
        $sueldo= mysqli_fetch_array($res3);
        //----------------------------------
        fwrite($file,
          limpia_espacios(
          $dato['segurosocial_sipe']).
          "0".$entidad['area'].
          $dato['nomposicion_id'].
          "\t\t\t\t".
          $sueldo['descuentos'].
          PHP_EOL);
      }
      fclose($file);
      $zip->addFile($descuento);


      // Para generar AUDITO.TXT dentro de BANCO.zip
      $sqlaudito = "SELECT SUM(a.monto) AS sueldo FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.codcon=100";
      $res3 = $db->query($sqlaudito,$conexion);
      $total_sueldo= mysqli_fetch_array($res3);
      //----------------------------------
      $file = fopen($audito, "w");
      fwrite($file,
        "0".$entidad['area'].
        "sede pla".
        "\t\t\t\t".
        $total_sueldo['sueldo'].
        "anr".
        PHP_EOL);
      fclose($file);
      $zip->addFile($audito);

      // Para generar PARTIDA.TXT dentro de BANCO.zip
      $sqlpartida = "SELECT SUM(a.monto) AS sueldo FROM nom_movimientos_nomina AS a WHERE a.codnom = '$nomina_id' AND a.codcon=100";
      $res3 = $db->query($sqlpartida,$conexion);
      $total_sueldo= mysqli_fetch_array($res3);
      //----------------------------------
      $file = fopen($partida, "w");
      fwrite($file,
        "2011".
        $area.
        $institucion.
        $tipo_presupuesto.
        $programa.
        $fuente.
        $sub_prog.
        $actividad.
        $objeto.
        "1".
        "001".
        "01".
        "16".
        "096".
        "0".$entidad['area'].
        $total_sueldo['sueldo'].
        PHP_EOL);
      fclose($file);
      $zip->addFile($partida);

      //Cerramos el archivo BANCO.zip
      $zip->close();
      //Procedemos a que se ejecute la descarga del archivo BANCO.zip
      header('Content-type: "application/zip"'); 
      header('Content-Disposition: attachment; filename='.$archivo); 
      readfile($archivo); 
      unlink($archivo);
      echo "Archivos generados adecuadamente. Consulte el archivo comprimido a descargar.";
      unlink($planilla);
      unlink($partida);
      unlink($descuento);
      unlink($audito);
}
?>
