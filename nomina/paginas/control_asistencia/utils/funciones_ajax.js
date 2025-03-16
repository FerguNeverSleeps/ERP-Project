function cambiarTurno( turno_id, ficha, n_turno, opc ){
    $.ajax({
        url  : 'ajax/server_side/cambiar_turno.php',
        type : 'POST',
        data : {
            turno_id:turno_id,
            ficha   :ficha,
            n_turno :n_turno,
            ajax    : true
        },
        dataType : 'json',
        success : function(response) {
            $('#turno_id').val(response['turno_id']);
            $('#turno_desc').val(response['turno']);
            calcularTiempo( $('#ficha').val(), $('#fec_ent').val(), $('#fec_sal').val(), $('#entrada').val(), $('#salida').val(), response['turno_id'], opc );
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
    });
}
function calcularTiempo( ficha, fec_ent, fec_sal, entrada, salida, turno_id, update ){
    $.ajax({
        url : 'ajax/procesar_registro.php',
        type : 'POST',
        data : {
            ficha   :ficha,
            fec_ent :fec_ent,
            fec_sal :fec_sal,
            entrada :entrada,
            salida  :salida,
            turno_id:turno_id,
            update  :update,
            ajax    : true
        },
        dataType : 'json',
        success : function(response) {
            //console.log(response);
            $('#tiempo').val(response['tiempo']);
            $('#tardanza').val(response['tardanza']);
            $('#h_extra').val(response['h_extra']);
            $('#recargo_25').val(response['recargo_25']);
            $('#recargo_50').val(response['recargo_50']);
            $('#jornada').val(response['jornada']);
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
    });
}
function calcularTurno(ficha,fecha){
    $.ajax({
        url : 'ajax/turnos.php',
        type : 'POST',
        data : {
            ficha   :ficha,
            fecha   :fecha,
            ajax    : true
        },
        dataType : 'json',
        success : function(response) {
            $('#turno_id').val(response['turno_id']);
            $('#turno').val(response['turno']);
            $('#jornada').val(response['tipo']);
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
    });
}
function validar_ea(fec_ent,fec_sal,entrada,salida){
    $.ajax({
        url : 'ajax/server_side/validar_campos.php',
        type : 'POST',
        data : {
            fec_ent:fec_ent,
            fec_sal:fec_sal,
            entrada:entrada,
            salida :salida,
            ajax   : true
        },
        dataType : 'json',
        success : function(response) {
            if (response['dato'] != 0) {
                alert('Error: '+response['msj']);
            }
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
    });
}
//SELECT COUNT(a.ficha) AS fichas,a.marca_reloj,b.codorg AS cod1,b.descrip AS nivel1,c.codorg AS cod2,c.descrip AS nivel2 FROM `nompersonal` AS a,nomnivel1 AS b,nomnivel2 AS c WHERE a.marca_reloj =0 AND a.codnivel1 = b.codorg AND a.codnivel2 = c.codorg GROUP BY a.codnivel1 ORDER BY a.codnivel1