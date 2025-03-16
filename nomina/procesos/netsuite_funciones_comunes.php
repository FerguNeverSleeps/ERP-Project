<?php
function letra_mes($n)
{
    switch ($n) {
        case '1':
        case '01':
            return "ene";
        case '2':
        case '02':
            return "feb";
        case '3':
        case '03':
            return "mar";
        case '4':
        case '04':
            return "abr";
        case '5':
        case '05':
            return "may";
        case '6':
        case '06':
            return "jun";
        case '7':
        case '07':
            return "jul";
        case '8':
        case '08':
            return "ago";
        case '9':
        case '09':
            return "sep";
        case '10':
            return "oct";
        case '11':
            return "nov";
        case '12':
            return "dic";
    }
    return "";
}

function preparar_fecha_contable($valor)
{
    list($mes, $anio) = explode('-', $valor);
    return '"' . letra_mes($mes) . $anio . '"';
}

function obtenerNiveles($conexion, $fecha_inicio, $fecha_fin, $nivelMayorA = 0)
{
    $sql_ejecutar = "
select nom_movimientos_nomina.codnivel1 from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom 
where nom_nominas_pago.periodo_ini >= '" . $fecha_inicio . "'
  and nom_nominas_pago.periodo_fin <= '" . $fecha_fin . "' 
    and nom_movimientos_nomina.codnivel1 > " . $nivelMayorA . "
group by nom_movimientos_nomina.codnivel1 ORDER BY CONVERT(nom_movimientos_nomina.codnivel1,UNSIGNED INTEGER) ASC
";

    $res = $conexion->query($sql_ejecutar);
    if ($res->num_rows == 0) {
        return [];
    }

    $niveles = [];
    while ($row = $res->fetch_array()) {
        $niveles [] = $row['codnivel1'];
    }

    return $niveles;
}

function obtenerSumatoriaPorConceptos($conexion, $conceptos, $fecha_inicio, $fecha_fin, $niveles)
{
    $conceptosSeparadoPorComa = join(', ', $conceptos);
    $nivelesSeparadoPorComa = join(', ', $niveles);
    $sql_ejecutar = "
select 
       case when nom_movimientos_nomina.tipcon = 'D' then sum(nom_movimientos_nomina.monto) else 0 end as 'credito',
       nom_movimientos_nomina.codnivel1
from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom
         inner join nomnivel1 on nomnivel1.codorg = nom_movimientos_nomina.codnivel1
         inner join subsidiaria s on nomnivel1.id_subsidiaria = s.id_subsidiaria
         inner join nomconceptos on nom_movimientos_nomina.codcon = nomconceptos.codcon
         left join cwconcue on nomconceptos.ctacon = cwconcue.Cuenta
where nom_nominas_pago.periodo_ini >= '" . $fecha_inicio . "'
  and nom_nominas_pago.periodo_fin <= '" . $fecha_fin . "'
  and nom_movimientos_nomina.tipcon <> 'P'
  and nom_movimientos_nomina.codcon in (" . $conceptosSeparadoPorComa . ") and
   nom_movimientos_nomina.codnivel1 in (" . $nivelesSeparadoPorComa . ")
group by nom_movimientos_nomina.codcon
";

    $res = $conexion->query($sql_ejecutar);
    if ($res->num_rows == 0) {
        return 0;
    }

    $fila = $res->fetch_array();

    return $fila > 0 ? $fila['credito'] : 0;
}

function obtenerSqlDeConceptosComunes($fecha_inicio, $fecha_fin, $niveles)
{
    $nivelesSeparadoPorComa = join(', ', $niveles);
    return "
select nom_nominas_pago.frecuencia,
       nom_nominas_pago.descrip                                                                        as id_externo,
       'US Dollar'                                                                                     as moneda,
       1                                                                                               as tipo_cambio,
       nom_nominas_pago.fecha,
       DATE_FORMAT(nom_nominas_pago.fecha, '%m-%Y')                                                    as fecha_contable,
       concat('0001#333', nom_movimientos_nomina.codcon, '-',
              date_format(nom_nominas_pago.fechapago, '%d-%m-%Y'))                                     as nota,
       s.descripcion                                                                                   as subsidiaria,
       concat(nom_movimientos_nomina.codcon, '-', nom_movimientos_nomina.descrip)                      as nota_linea,
       cwconcue.id                                                                                     as id_cuenta_contable,
       cwconcue.Cuenta                                                                                 as numero_cuenta_contable,
       nom_movimientos_nomina.codcon,
       case when nom_movimientos_nomina.tipcon = 'A' then sum(nom_movimientos_nomina.monto) else 0 end as 'debito',
       case when nom_movimientos_nomina.tipcon = 'D' then sum(nom_movimientos_nomina.monto) else 0 end as 'credito',
       null                                                                                            as external_id_tercero,
       null                                                                                            as nombre_tercero,
       null                                                                                            as clase,
       null                                                                                            as departamento,
       null                                                                                            as ubicacion_centro_costo
from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom
         inner join nomnivel1 on nomnivel1.codorg = nom_movimientos_nomina.codnivel1
         inner join subsidiaria s on nomnivel1.id_subsidiaria = s.id_subsidiaria
         inner join nomconceptos on nom_movimientos_nomina.codcon = nomconceptos.codcon
         left join cwconcue on nomconceptos.ctacon = cwconcue.Cuenta
where periodo_ini >= '" . $fecha_inicio . "'
  and periodo_fin <= '" . $fecha_fin . "'
  and nom_movimientos_nomina.tipcon <> 'P'
  and nom_movimientos_nomina.codcon not in (180, 185, 187,190, 195,196,198,199) and nom_movimientos_nomina.codnivel1 in (" . $nivelesSeparadoPorComa . ")
group by nom_movimientos_nomina.codcon 
    ";
}

function obtenerSqlDeConceptosPatronales($fecha_inicio, $fecha_fin, $niveles)
{
    $nivelesSeparadoPorComa = join(', ', $niveles);
    return "
select nom_nominas_pago.frecuencia,
       nom_nominas_pago.descrip                                                                        as id_externo,
       'US Dollar'                                                                                     as moneda,
       1                                                                                               as tipo_cambio,
       nom_nominas_pago.fecha,
       DATE_FORMAT(nom_nominas_pago.fecha, '%m-%Y')                                                    as fecha_contable,
       concat('0001#333', nom_movimientos_nomina.codcon, '-',
              date_format(nom_nominas_pago.fechapago, '%d-%m-%Y'))                                     as nota,
       s.descripcion                                                                                   as subsidiaria,
       concat(nom_movimientos_nomina.codcon, '-', nom_movimientos_nomina.descrip)                      as nota_linea,
       cwconcue.id                                                                                     as id_cuenta_contable,
       cwconcue.Cuenta                                                                                 as numero_cuenta_contable,
       sum(nom_movimientos_nomina.monto) as 'debito',
       0 as 'credito',
       null                                                                                            as external_id_tercero,
       null                                                                                            as nombre_tercero,
       null                                                                                            as clase,
       null                                                                                            as departamento,
       null                                                                                            as ubicacion_centro_costo,
       1 as orden_asiento
from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom
         inner join nomnivel1 on nomnivel1.codorg = nom_movimientos_nomina.codnivel1
         inner join subsidiaria s on nomnivel1.id_subsidiaria = s.id_subsidiaria
         inner join nomconceptos on nom_movimientos_nomina.codcon = nomconceptos.codcon
         left join cwconcue on nomconceptos.ctacon = cwconcue.Cuenta
where periodo_ini >= '" . $fecha_inicio . "'
  and periodo_fin <= '" . $fecha_fin . "'
  and nom_movimientos_nomina.tipcon = 'P'
  and nom_movimientos_nomina.codnivel1 in (" . $nivelesSeparadoPorComa . ")
group by nom_movimientos_nomina.codcon

union 

select nom_nominas_pago.frecuencia,
       nom_nominas_pago.descrip                                                                        as id_externo,
       'US Dollar'                                                                                     as moneda,
       1                                                                                               as tipo_cambio,
       nom_nominas_pago.fecha,
       DATE_FORMAT(nom_nominas_pago.fecha, '%m-%Y')                                                    as fecha_contable,
       concat('0001#333', nom_movimientos_nomina.codcon, '-',
              date_format(nom_nominas_pago.fechapago, '%d-%m-%Y'))                                     as nota,
       s.descripcion                                                                                   as subsidiaria,
       concat(nom_movimientos_nomina.codcon, '-', nom_movimientos_nomina.descrip, ' Por Pagar')        as nota_linea,
       cwconcue.id                                                                                     as id_cuenta_contable,
       cwconcue.Cuenta                                                                                 as numero_cuenta_contable,
       0 as 'debito',
       sum(nom_movimientos_nomina.monto) as 'credito',
       null                                                                                            as external_id_tercero,
       null                                                                                            as nombre_tercero,
       null                                                                                            as clase,
       null                                                                                            as departamento,
       null                                                                                            as ubicacion_centro_costo,
       2 as orden_asiento
from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom
         inner join nomnivel1 on nomnivel1.codorg = nom_movimientos_nomina.codnivel1
         inner join subsidiaria s on nomnivel1.id_subsidiaria = s.id_subsidiaria
         inner join nomconceptos on nom_movimientos_nomina.codcon = nomconceptos.codcon
         left join cwconcue on nomconceptos.ctacon1 = cwconcue.Cuenta
where periodo_ini >= '" . $fecha_inicio . "'
  and periodo_fin <= '" . $fecha_fin . "'
  and nom_movimientos_nomina.tipcon = 'P'
  and nom_movimientos_nomina.codnivel1 in (" . $nivelesSeparadoPorComa . ")
group by nom_movimientos_nomina.codcon

order by nota, orden_asiento
";
}

function obtenerSqlDeConceptos180($fecha_inicio, $fecha_fin, $niveles)
{
    $nivelesSeparadoPorComa = join(', ', $niveles);
    return "
select nom_nominas_pago.frecuencia,
       nom_nominas_pago.descrip                                                                        as id_externo,
       'US Dollar'                                                                                     as moneda,
       1                                                                                               as tipo_cambio,
       nom_nominas_pago.fecha,
       DATE_FORMAT(nom_nominas_pago.fecha, '%m-%Y')                                                    as fecha_contable,
       concat('0001#333', nom_movimientos_nomina.codcon, '-',
              date_format(nom_nominas_pago.fechapago, '%d-%m-%Y'))                                     as nota,
       s.descripcion                                                                                   as subsidiaria,
       concat(nom_movimientos_nomina.codcon, '-', nom_movimientos_nomina.descrip)                      as nota_linea,
       cwconcue.id                                                                                     as id_cuenta_contable,
       cwconcue.Cuenta                                                                                 as numero_cuenta_contable,
       nom_movimientos_nomina.codcon,
       case when nom_movimientos_nomina.tipcon = 'A' then nom_movimientos_nomina.monto else 0 end as 'debito',
       case when nom_movimientos_nomina.tipcon = 'D' then nom_movimientos_nomina.monto else 0 end as 'credito',
       nom_movimientos_nomina.ficha                                                                    as external_id_tercero,
       nompersonal.apenom                                                                             as nombre_tercero,
       null                                                                                            as clase,
       null                                                                                            as departamento,
       null                                                                                            as ubicacion_centro_costo
from nom_nominas_pago
         inner join nom_movimientos_nomina on nom_movimientos_nomina.codnom = nom_nominas_pago.codnom
         inner join nompersonal on nompersonal.ficha = nom_movimientos_nomina.ficha
         inner join nomnivel1 on nomnivel1.codorg = nom_movimientos_nomina.codnivel1
         inner join subsidiaria s on nomnivel1.id_subsidiaria = s.id_subsidiaria
         inner join nomconceptos on nom_movimientos_nomina.codcon = nomconceptos.codcon
         left join cwconcue on nomconceptos.ctacon = cwconcue.Cuenta
where periodo_ini >= '" . $fecha_inicio . "'
  and periodo_fin <= '" . $fecha_fin . "'
  and nom_movimientos_nomina.codcon = 180 and nom_movimientos_nomina.codnivel1 in (" . $nivelesSeparadoPorComa . ")
";
}

function construirAsiento($fila, $delimitador)
{
    return join($delimitador, [
        $fila['frecuencia'],
        $fila['id_externo'],
        $fila['moneda'],
        $fila['tipo_cambio'],
        $fila['fecha'],
        preparar_fecha_contable($fila['fecha_contable']),
        $fila['nota'],
        $fila['subsidiaria'],
        $fila['nota_linea'],
        $fila['id_cuenta_contable'],
        $fila['numero_cuenta_contable'],
        $fila['debito'],
        $fila['credito'],
        $fila['external_id_tercero'],
        $fila['nombre_tercero'],
        $fila['clase'],
        $fila['departamento'],
        $fila['ubicacion_centro_costo']
    ]);
}

?>