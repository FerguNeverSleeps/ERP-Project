//CLASE USADA PARA ALMACENAR MARCACIONES
class class_reloj_detalle {

  constructor(data){

    this.id = (data.id != undefined && data.id != null) ? data.id : ''
    this.id_nomcalendarios_personal = (data.id_nomcalendarios_personal != undefined && data.id_nomcalendarios_personal != null) ? data.id_nomcalendarios_personal : ''
    this.fecha = (data != undefined) ? data.fecha : ''
    this.turno = (data != undefined) ? data.turno : ''
    this.entrada = (data.entrada != undefined && data.entrada != null) ? data.entrada : '00:00:00'
    this.salmuerzo = (data.salmuerzo != undefined && data.salmuerzo != null) ? data.salmuerzo : '00:00:00'
    this.ealmuerzo = (data.ealmuerzo != undefined && data.ealmuerzo != null) ? data.ealmuerzo : '00:00:00'
    this.salida = (data.salida != undefined && data.salida != null) ? data.salida : '00:00:00'
    this.sobretiempo_aprobado = (data.sobretiempo_aprobado != undefined && data.sobretiempo_aprobado != null) ? data.sobretiempo_aprobado : '00:00'
    this.sobretiempo_real = (data.sobretiempo_real != undefined) ? data.sobretiempo_real : '00:00'
    this.extra = (data.extra != undefined && data.extra != null) ? data.extra : '00:00'
    this.extraext = (data.extraext != undefined && data.extraext != null) ? data.extraext : '00:00'
    this.extranoc = (data.extranoc != undefined && data.extranoc != null) ? data.extranoc : '00:00'
    this.extraextnoc = (data.extraextnoc != undefined && data.extraextnoc != null) ? data.extraextnoc : '00:00'
    this.mixtodiurna = (data.mixtodiurna != undefined && data.mixtodiurna != null) ? data.mixtodiurna : '00:00'
    this.mixtoextdiurna = (data.mixtoextdiurna != undefined && data.mixtoextdiurna != null) ? data.mixtoextdiurna : '00:00'
    this.mixtonoc = (data.mixtonoc != undefined && data.mixtonoc != null) ? data.mixtonoc : '00:00'
    this.mixtoextnoc = (data.mixtoextnoc != undefined && data.mixtoextnoc != null) ? data.mixtoextnoc : '00:00'
    this.extra_apr = (data.extra_apr != undefined && data.extra_apr != null) ? data.extra_apr : '00:00'
    this.extraext_apr = (data.extraext_apr != undefined && data.extraext_apr != null) ? data.extraext_apr : '00:00'
    this.extranoc_apr = (data.extranoc_apr != undefined && data.extranoc_apr != null) ? data.extranoc_apr : '00:00'
    this.extraextnoc_apr = (data.extraextnoc_apr != undefined && data.extraextnoc_apr != null) ? data.extraextnoc_apr : '00:00'
    this.extramixdiurna_apr = (data.extramixdiurna_apr != undefined && data.extramixdiurna_apr != null) ? data.extramixdiurna_apr : '00:00'
    this.extraextmixdiurna_apr = (data.extraextmixdiurna_apr != undefined && data.extraextmixdiurna_apr != null) ? data.extraextmixdiurna_apr : '00:00'
    this.extramixnoc_apr = (data.extramixnoc_apr != undefined && data.extramixnoc_apr != null) ? data.extramixnoc_apr : '00:00'
    this.extraextmixnoc_apr = (data.extraextmixnoc_apr != undefined && data.extraextmixnoc_apr != null) ? data.extraextmixnoc_apr : '00:00'
    this.mixtoextnocnac = (data.mixtoextnocnac != undefined && data.mixtoextnocnac != null) ? data.mixtoextnocnac : '00:00'
    this.mixtoextnocnac_apr = (data.mixtoextnocnac_apr != undefined && data.mixtoextnocnac_apr != null) ? data.mixtoextnocnac_apr : '00:00'
    this.tardanza = (data.tardanza != undefined && data.tardanza != null) ? data.tardanza : '00:00'
    this.domingo = (data.domingo != undefined && data.domingo != null) ? data.domingo : '00:00'
    this.nacional = (data.nacional != undefined && data.nacional != null) ? data.nacional : '00:00'
    this.ficha = (data != undefined) ? data.ficha : ''
    this.ordinaria = (data.ordinaria != undefined && data.ordinaria != null) ? data.ordinaria : '00:00'
    this.id_encabezado = (data != undefined) ? data.id_encabezado : ''
    this.observacion = (data.observacion != undefined && data.observacion != null) ? data.observacion : ''
    this.id_incidencia = (data.id_incidencia != undefined && data.id_incidencia != null) ? data.id_incidencia : 0
    this.cantidad_incidencia = (data.cantidad_incidencia != undefined && data.cantidad_incidencia != null) ? data.cantidad_incidencia : '00:00'
    this.horas_reales = (data.horas_reales != undefined && data.horas_reales != null) ? data.horas_reales : '00:00'
    this.horas_teoricas = (data.horas_teoricas != undefined && data.horas_teoricas != null) ? data.horas_teoricas : '00:00'
    this.agregado = (data.agregado != undefined && data.agregado != null) ? data.agregado : 0
    this.marcacion_disp_id = (data.marcacion_disp_id != undefined && data.marcacion_disp_id != null) ? data.marcacion_disp_id : 0
    this.entrada_turno = (data.entrada_turno != undefined && data.entrada_turno != null) ? data.entrada_turno : '00:00'
    this.salida_turno = (data.salida_turno != undefined && data.salida_turno != null) ? data.salida_turno : '00:00'
    this.marca_reloj = (data.marca_reloj != undefined && data.marca_reloj != null) ? data.marca_reloj : 0
    this.estado = (data.estado != undefined && data.estado != null) ? data.estado : ''
    this.fecha_formateada_dia = (data.fecha_formateada_dia != undefined && data.fecha_formateada_dia != null) ? data.fecha_formateada_dia : ''
    this.salida_diasiguiente = (data.salida_diasiguiente != undefined && data.salida_diasiguiente != null) ? data.salida_diasiguiente : ''
    this.editado = 0
    this.nuevo_registro = false
  }

}

//CLASE USADA PARA ALMACENAR MARCACIONES VACIAS
class class_reloj_detalle_vacio {

  constructor(data){

    this.id = ''
    this.id_nomcalendarios_personal = (data.id_nomcalendarios_personal != undefined && data.id_nomcalendarios_personal != null) ? data.id_nomcalendarios_personal : ''
    this.fecha = (data != undefined) ? data.fecha : ''
    this.turno = (data != undefined) ? data.turno : ''
    this.entrada = '00:00:00'
    this.salmuerzo = '00:00:00'
    this.ealmuerzo = '00:00:00'
    this.salida = '00:00:00'
    this.sobretiempo_aprobado = '00:00'
    this.sobretiempo_real = '00:00'
    this.extra = '00:00'
    this.extraext = '00:00'
    this.extranoc = '00:00'
    this.extraextnoc = '00:00'
    this.mixtodiurna = '00:00'
    this.mixtoextdiurna = '00:00'
    this.mixtonoc = '00:00'
    this.mixtoextnoc = '00:00'
    this.extra_apr = '00:00'
    this.extraext_apr = '00:00'
    this.extranoc_apr = '00:00'
    this.extraextnoc_apr = '00:00'
    this.extramixdiurna_apr = '00:00'
    this.extraextmixdiurna_apr = '00:00'
    this.extramixnoc_apr = '00:00'
    this.extraextmixnoc_apr = '00:00'
    this.mixtoextnocnac = '00:00'
    this.mixtoextnocnac_apr = '00:00'
    this.tardanza = '00:00'
    this.domingo = '00:00'
    this.nacional = '00:00'
    this.ficha = (data != undefined) ? data.ficha : ''
    this.ordinaria = '00:00'
    this.id_encabezado = (data != undefined) ? data.id_encabezado : ''
    this.observacion = ''
    this.id_incidencia = 0
    this.cantidad_incidencia = '00:00'
    this.horas_reales = '00:00'
    this.horas_teoricas = '00:00'
    this.agregado = 1
    this.salida_diasiguiente = ''
    this.editado = 0
  }

}