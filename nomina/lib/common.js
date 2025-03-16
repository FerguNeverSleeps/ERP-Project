function marcartodos(check_global,check_hijo){
	var opcion_global=document.getElementById(check_global)
	
	
	for(i=0; ele=document.frmPrincipal.elements[i]; i++){  		
		if (ele.id==check_hijo)
			{ele.checked =opcion_global.checked;}			
	}	

}
function MarcarTodos(valores)
{
	if (document.frmPrincipal.marcar_todos.value==1)
		{Opcion=true;document.frmPrincipal.marcar_todos.value=0;}
	else
		{Opcion=false;document.frmPrincipal.marcar_todos.value=1;}

	for(i=0; ele=document.frmPrincipal.elements[i]; i++){  		
		if (ele.name==valores)
			{ele.checked =Opcion;}			
	}	
}

function validar_montoacausar(monto_odp,monto_escrito){

if(monto_escrito>monto_odp){
alert("El monto ingresado no puede ser mayor al de la orden de pago, por favor revise los montos")
}

}


function paginacion(url,valor,campos){

document.location.href=url+".php?pagina="+valor+"&"+campos
}


function actualizar(seleccion){
	var opcion=document.getElementsByTagName("input");

	for (i=0; i<opcion.length; i++){
		if (opcion[i].type == "radio" && opcion[i].name != seleccion.name) {
			opcion[i].checked = false;
		}
	}	
}
function regresar(){
	document.location.href='configuracion.php'
}

function habilitar(valor){

	var opcion=document.getElementsByTagName("input");
	
	for (i=0; i<opcion.length; i++){
		if (opcion[i].type == "radio" && opcion[i].value == valor) {
			opcion[i].checked = true
			
		}
		else{
			opcion[i].disabled=true
		}
	}
}

function confirmar(msg,url) {
 self.rValue = false;
 if (confirm(msg) == true) {
  window.location.href = url;
 }
}

function over(element,estilo){
	element.addClassName(estilo);
}
function out(element,estilo){
	element.removeClassName(estilo);
}

function copytext(s) {
	window.clipboardData.setData("Text", s);
}

function abrirAjax()
{ 
	
	var xmlhttp=false;
	try
	{
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(e)
	{
		try
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(E)
		{
			if (!xmlhttp && typeof XMLHttpRequest!='undefined') xmlhttp=new XMLHttpRequest();
		}
	}
	return xmlhttp; 
}

function imprimir(nombre){
	var ficha = document.getElementById(nombre)
	var ventimp = window.open(' ', 'popimpr','width=10, height=10, TOOLBAR=NO, LOCATION=NO, MENUBAR=NO, SCROLLBARS=NO, RESIZABLE=NO')
var estilo="<link href=\"../estilos.css\" rel=\"stylesheet\" type=\"text/css\">"
ventimp.document.write( estilo )
	ventimp.document.write( ficha.innerHTML )
	ventimp.document.close()
	ventimp.print()
	ventimp.close()
}
function cargar_municipio(){
 	
 	var estado=document.getElementById('cod_estado')
	var municipio=document.getElementById('cod_municipio')
	var contenido_municipio=abrirAjax()
	contenido_municipio.open("GET", "procesos.php?estado="+estado.value, true)
  	contenido_municipio.onreadystatechange=function() 
	{
		if (contenido_municipio.readyState==1)
		{
			municipio.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Calculando..."
			municipio.appendChild(opcion)
			municipio.disabled=true
		}
		if (contenido_municipio.readyState==4)
		{
			municipio.parentNode.innerHTML = contenido_municipio.responseText;
		}
	}
	contenido_municipio.send(null);
}
function cargar_programa(){
	var sector=document.getElementById('sel_sector')
	var programa=document.getElementById('sel_programa')
	var contenido_programa=abrirAjax()
	contenido_programa.open("GET", "procesos.php?accion=1&sector="+sector.value, true)
   contenido_programa.onreadystatechange=function() 
	{
		if (contenido_programa.readyState==1)
		{
			programa.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			programa.appendChild(opcion)
			programa.disabled=true
		}
		if (contenido_programa.readyState==4)
		{
			programa.parentNode.innerHTML = contenido_programa.responseText;
		} 
	}
	contenido_programa.send(null);
}
function cargar_actividad(){
 	var sector=document.getElementById('sel_sector')
	var programa=document.getElementById('sel_programa')
	var actividad=document.getElementById('sel_actividad')
	
	var contenido_actividad=abrirAjax()
	
	contenido_actividad.open("GET", "procesos.php?accion=2&sector="+sector.value+"&programa="+programa.value, true)
   	contenido_actividad.onreadystatechange=function() 
	{ 
		if (contenido_actividad.readyState==1)
		{
			actividad.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			actividad.appendChild(opcion)
			actividad.disabled=true
		}
		if (contenido_actividad.readyState==4)
		{
			actividad.parentNode.innerHTML = contenido_actividad.responseText;
		}
	}
	contenido_actividad.send(null);
}
function cargar_partida(){
 	var sector=document.getElementById('sel_sector')
	var programa=document.getElementById('sel_programa')
	var actividad=document.getElementById('sel_actividad')
	var partida=document.getElementById('cod')
	var contenido_partida=abrirAjax()
	
	contenido_partida.open("GET", "procesos.php?accion=3&sector="+sector.value+"&programa="+programa.value+"&actividad="+actividad.value, true)
   	contenido_partida.onreadystatechange=function() 
	{
		if (contenido_partida.readyState==4)
		{
			partida.parentNode.innerHTML = contenido_partida.responseText;
		}
	}
	contenido_partida.send(null);
}

function periodo2()
{
	var frecuencia=document.getElementById('cboFrecuencia')
	
	var tdperiodo=document.getElementById('periodo')
	//alert(frecuencia.value)
	var contenido_tdperiodo=abrirAjax()
	contenido_tdperiodo.open("GET", "procesos.php?frecuencia="+frecuencia.value+"&opcion=1", true)
   	contenido_tdperiodo.onreadystatechange=function() 
	{
		if (contenido_tdperiodo.readyState==4)
		{
			tdperiodo.parentNode.innerHTML = contenido_tdperiodo.responseText;
		}
	}
	contenido_tdperiodo.send(null);
	
}

function cargar_fecha()
{
	var periodo=document.getElementById('sel_periodo')
	var frecuencia=document.getElementById('cboFrecuencia')
	var fechas=document.getElementById('fechas')
	//alert(frecuencia.value)
	var contenido_fechas=abrirAjax()
	contenido_fechas.open("GET", "procesos.php?frecuencia="+frecuencia.value+"&opcion=2&periodo="+periodo.value, true)
   	contenido_fechas.onreadystatechange=function() 
	{
		if (contenido_fechas.readyState==4)
		{
			fechas.parentNode.innerHTML = contenido_fechas.responseText;
		}
	}
	contenido_fechas.send(null);	
}

function cargar_tipo()
{
	var tipo=document.getElementById('tipo_registro')
	var cedula=document.getElementById('cedula')
	var codigo=document.getElementById('codigo')
	var tipotipo=document.getElementById('registro')
	//var fechas=document.getElementById('fechas')
	//alert(frecuencia.value)
	var contenido_tipotipo=abrirAjax()
	contenido_tipotipo.open("GET", "../paginas/procesos.php?opcion="+tipo.value+"&cedula="+cedula.value+"&codigo="+codigo.value, true)
   	contenido_tipotipo.onreadystatechange=function()
	{
		if (contenido_tipotipo.readyState==4)
		{
			tipotipo.parentNode.innerHTML = contenido_tipotipo.responseText;
		}
	}
	contenido_tipotipo.send(null);
}

function alerta(id,dia,anio)
{
	var celda=document.getElementById(id)	
	var laborable= document.getElementById('estado')
	var contenido_celda=abrirAjax()
	contenido_celda.open("GET", "modificar_calendarios.php?fecha="+id+"&estado="+laborable.value+"&dia="+dia, true)
   	contenido_celda.onreadystatechange=function() 
	{
		if (contenido_celda.readyState==4)
		{
			celda.parentNode.innerHTML = contenido_celda.responseText;
		}
	}
	contenido_celda.send(null);
	document.location.href="calendarios.php?estado="+laborable.value+"&ano="+anio//parent.cont
	//alert("FUNCIONA"+celda+id+dia+laborable.value);
}

function alerta_personal(id,dia,anio,ficha)
{
	var celda=document.getElementById(id)	
	var laborable= document.getElementById('estado')
	var turno= document.getElementById('turno')
	var contenido_celda=abrirAjax()
	contenido_celda.open("GET", "modificar_calendarios.php?fecha="+id+"&estado="+laborable.value+"&dia="+dia+"&ficha="+ficha+"&per=1&turno="+turno.value, true)
   	contenido_celda.onreadystatechange=function() 
	{
		if (contenido_celda.readyState==4)
		{
			celda.parentNode.innerHTML = contenido_celda.responseText;
			document.location.href="calendarios_personal.php?ano="+anio+"&ficha="+ficha+"&turnoss="+turno.value//parent.cont
			//alert(contenido_celda.responseText)
		}
	}
	contenido_celda.send(null);
	//parent.cont.location.href="calendarios_personal.php?ano="+anio+"&ficha="+ficha
	//alert("FUNCIONA"+celda+id+dia+laborable.value);
}

function alerta_personal_ajax(id,dia,anio,ficha)
{
	var celda=document.getElementById(id)	
	var laborable= document.getElementById('estado_dia')
	var turno= document.getElementById('turno')

	//console.log("Año = " + anio + " Ficha: " + ficha + " Turno: " + turno.value);
	var contenido_celda=abrirAjax()
	contenido_celda.open("GET", "modificar_calendarios.php?fecha="+id+"&estado="+laborable.value+"&dia="+dia+"&ficha="+ficha+"&per=1&turno="+turno.value, true)
   	contenido_celda.onreadystatechange=function() 
	{
		if (contenido_celda.readyState==4)
		{
			celda.parentNode.innerHTML = contenido_celda.responseText;

			var iframe = window.parent.document.getElementById('iframe_tab_1');
			if (iframe === null)
			{
				//console.log('Ext.getCmp');
				Ext.getCmp('tab-calendario').loader.url = "calendarios_personal_ajax.php?ano=" + anio + "&ficha=" + ficha + "&turnoss=" + turno.value;
    			Ext.getCmp('tab-calendario').loader.load();
			}
			else
			{
				//console.log('iframe:');
				iframe.src = "calendarios_personal_ajax.php?ano=" + anio + "&ficha=" + ficha + "&turnoss=" + turno.value;
			}
		}
	}
	contenido_celda.send(null);
	//parent.cont.location.href="calendarios_personal.php?ano="+anio+"&ficha="+ficha
	//alert("FUNCIONA"+celda+id+dia+laborable.value);
}

function cargar_sueldo(){
 	var monto=document.getElementById('txtmonto')
	var cargo=document.getElementById('cboCargos')
	var paso=document.getElementById('txtpaso')
	
	var contenido_monto=abrirAjax()
	
	contenido_monto.open("GET", "procesos.php?opcion=3&cargo="+cargo.value+"&paso="+paso.value, true)
   	contenido_monto.onreadystatechange=function() 
	{ 
		if (contenido_monto.readyState==1)
		{
			monto.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			monto.appendChild(opcion)
			//monto.disabled=true
		}
		if (contenido_monto.readyState==4)
		{
			//monto.value = contenido_monto.responseText;
			monto.parentNode.innerHTML = contenido_monto.responseText;
		}
	}
	contenido_monto.send(null);
}


function cargar_ccosto(){
 	var unidad=document.getElementById('unidad')
	var ccosto=document.getElementById('ccosto')
	//var actividad=document.getElementById('sel_actividad')
	
	var contenido_ccosto=abrirAjax()
	
	contenido_ccosto.open("GET", "ccosto.php?opcion=1&unidad="+unidad.value, true)
   	contenido_ccosto.onreadystatechange=function() 
	{ 
		if (contenido_ccosto.readyState==1)
		{
			ccosto.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			ccosto.appendChild(opcion)
			ccosto.disabled=true
		}
		if (contenido_ccosto.readyState==4)
		{
			ccosto.parentNode.innerHTML = contenido_ccosto.responseText;
		}
	}
	contenido_ccosto.send(null);
}


function cargar_nombre(){
 	var nombre=document.getElementById('nombre')
	var ficha=document.getElementById('ficha')
	nombre.innerHTML =''
	
	var contenido_nombre=abrirAjax()
	
	contenido_nombre.open("GET", "procesos2.php?opcion=1&ficha="+ficha.value, true)
   	contenido_nombre.onreadystatechange=function() 
	{ 
		if (contenido_nombre.readyState==1)
		{
			nombre.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			nombre.appendChild(opcion)
			//monto.disabled=true
		}
		if (contenido_nombre.readyState==4)
		{
			//monto.value = contenido_monto.responseText;
			nombre.parentNode.innerHTML = contenido_nombre.responseText;
		}
	}
	contenido_nombre.send(null);
}

function cargar_concepto(){
 	var nombre=document.getElementById('concepto')
	var concepto=document.getElementById('codcon')
	var contenido_nombre=abrirAjax()
	
	contenido_nombre.open("GET", "procesos2.php?opcion=2&concepto="+concepto.value, true)
   	contenido_nombre.onreadystatechange=function() 
	{ 
		if (contenido_nombre.readyState==1)
		{
			nombre.length=0
			var opcion=document.createElement("opcion")
			opcion.value=0
			opcion.innerHTML= "Cargando..."
			nombre.appendChild(opcion)
			//monto.disabled=true
		}
		if (contenido_nombre.readyState==4)
		{
			//monto.value = contenido_monto.responseText;
			nombre.innerHTML =''
			nombre.parentNode.innerHTML = contenido_nombre.responseText;
		}
	}
	contenido_nombre.send(null);
}

function cargar_horas(){
	var reg=document.getElementById('reg');
	var empresa=document.getElementById('empresa');
 	var id=document.getElementById('id');
 	var marcacion_disp_id=document.getElementById('dispositivo');
 	var fecha=document.getElementById('fecha');
	var entrada=document.getElementById('entrada');
	var salmuerzo=document.getElementById('salmuerzo');
	var ealmuerzo=document.getElementById('ealmuerzo');
	var salida=document.getElementById('salida');
	var salida_diasiguiente=document.getElementById('salida_diasiguiente');
	var acum_semanal=document.getElementById('acum_semanal');
	var turno_id=document.getElementById('turno_id');
	var select_turno_id=document.getElementById('select_turno_id');
	if(empresa.value=="pandora")
	{
		var entrada1=document.getElementById('entrada1');
		var salida1=document.getElementById('salida1');
	}
	
	var descextra1=document.getElementById('descextra1');
	var ordinaria=document.getElementById('ordinaria');
	var tardanza=document.getElementById('tardanza');
	var extra=document.getElementById('extra');
	var extraext=document.getElementById('extraext');
	var extranoc=document.getElementById('extranoc');
	var extraextnoc=document.getElementById('extraextnoc');
	var domingo=document.getElementById('domingo');
	var estado=document.getElementById('estado');
	var nacional=document.getElementById('nacional');
	//var extranac=document.getElementById('extranac');
	//var extranocnac=document.getElementById('extranocnac');
	var extramixdiurna=document.getElementById('extramixdiurna');
	var extraextmixdiurna=document.getElementById('extraextmixdiurna');
	var extramixnoc=document.getElementById('extramixnoc');
//	var emergencia=document.getElementById('emergencia');
	var descansoincompleto=document.getElementById('descansoincompleto');
	var dialibre=document.getElementById('dialibre');

	var ficha=document.getElementById('ficha');
	var horas_reales=document.getElementById('horas_reales');
	var horas_teoricas=document.getElementById('horas_teoricas');

	var contenido=abrirAjax()
	
	if(empresa.value=="pandora")
	{
		contenido.open("GET", "procesos_reloj.php?opcion="+empresa.value+"&cod_enca="+reg.value+"&marcacion_disp_id="+marcacion_disp_id.value+"&id="+id.value+"&entrada="+entrada.value+"&salmuerzo="+salmuerzo.value+"&ealmuerzo="+ealmuerzo.value+"&salida="+salida.value+"&entrada1="+entrada1.value+"&salida1="+salida1.value+"&fecha="+fecha.value+"&ficha="+ficha.value, true)
	}
	else if(empresa.value=="electron")
	{
		contenido.open("GET", "procesos_reloj.php?opcion="+empresa.value+"&cod_enca="+reg.value+"&marcacion_disp_id="+marcacion_disp_id.value+"&id="+id.value+"&entrada="+entrada.value+"&salmuerzo="+salmuerzo.value+"&ealmuerzo="+ealmuerzo.value+"&salida="+salida.value+"&fecha="+fecha.value+"&ficha="+ficha.value+"&ent_emer="+ent_emer.value+"&sal_emer="+sal_emer.value+"&salida_diasiguiente="+salida_diasiguiente.value, true)
	}
        else if(empresa.value=="zkteco")
	{
		contenido.open("GET", "procesos_reloj.php?opcion="+empresa.value+"&cod_enca="+reg.value+"&marcacion_disp_id="+marcacion_disp_id.value+"&id="+id.value+"&entrada="+entrada.value+"&salmuerzo="+salmuerzo.value+"&ealmuerzo="+ealmuerzo.value+"&salida="+salida.value+"&fecha="+fecha.value+"&ficha="+ficha.value+"&salida_diasiguiente="+salida_diasiguiente.value, true)
	}
   contenido.onreadystatechange=function() 
	{ 
		if (contenido.readyState==1)
		{
			estado.value="Calculando...";
		}
		if (contenido.readyState==4)
		{
			var cadena=contenido.responseText;
			//alert(cadena);
			var separada= cadena.split('--')
			estado.value=separada[0];
			ordinaria.value =separada[1]?separada[1]:"00:00";
			extra.value =separada[2]?separada[2]:"00:00";
			domingo.value =separada[3]?separada[3]:"00:00";
			tardanza.value =separada[4]?separada[4]:"00:00";
			extraext.value =separada[5]?separada[5]:"00:00";
			extranoc.value =separada[6]?separada[6]:"00:00";
			extraextnoc.value =separada[7]?separada[7]:"00:00";
			nacional.value = separada[8]?separada[8]:"00:00";
			//extranac.value = separada[9];
			//extranocnac.value = separada[10];
			descextra1.value = separada[9]?separada[9]:"00:00";
			extramixdiurna.value = separada[10]?separada[10]:"00:00";
			extraextmixdiurna.value = separada[11]?separada[11]:"00:00";
			extramixnoc.value = separada[12]?separada[12]:"00:00";
			extraextmixnoc.value = separada[13]?separada[13]:"00:00";
            if(empresa.value=="electron")
            {
                emergencia.value = separada[14]?separada[14]:"00:00";
                descansoincompleto.value = separada[15]?separada[15]:"00:00";
            }
			dialibre.value = separada[16]?separada[16]:"00:00";
			acum_semanal.value=separada[17]?separada[17]:"00:00";
			turno_id.value=separada[18]?separada[18]:"0";
			if(select_turno_id){
				select_turno_id.value=turno_id.value;				
				if(turno_json[turno_id.value] && turno_json[turno_id.value]["horario"]){
                    document.getElementById("turno_tipo").innerHTML       = turno_json[turno_id.value]["horario"];
                    document.getElementById("turno_horas_reales").value   = turno_json[turno_id.value]["turno_horas_reales"];
					document.getElementById("turno_horas_teoricas").value = turno_json[turno_id.value]["turno_horas_teoricas"];
                }
			}
			horas_reales.value=separada[21]?separada[21]:"";
			horas_teoricas.value=separada[22]?separada[22]:"";

			
			mostrar_porcentajes();
		}
	}
	contenido.send(null);
}

function mostrar_porcentajes(){
	var ordinaria=document.getElementById('ordinaria');
	var domingo=document.getElementById('domingo');
	var nacional=document.getElementById('nacional');

	//colocar los %
	var dia_semana=null;
	if(document.getElementById("fecha").value && document.getElementById("fecha").value!="0000-00-00"){
		var dt_fecha=new Date(document.getElementById("fecha").value+" 00:00:00");
		//console.log(dt_fecha);
		var dia_semana=dt_fecha.getDay();
	}
	if(domingo.value!="00:00" && domingo.value!=""){//domingo
		//si es dia siguiente, la fecha es domingo, y las horas son ordinaria (usar la tabla de % de ordinaria)
		if(document.getElementById("salida_diasiguiente").value=="SI" && dia_semana===0 && ordinaria.value!="00:00" && ordinaria.value!=""){
			mostrar_porcentajes_ordinaria();
		}
		else{
			mostrar_porcentajes_domingo();
		}
	}
	else if(nacional.value!="00:00" && nacional.value!=""){//nacional
		mostrar_porcentajes_nacional();
	}
	else{//regular
		mostrar_porcentajes_ordinaria();
	}

}

function mostrar_porcentajes_ordinaria(){
	document.getElementById('porcentaje_extra').innerHTML="1.25%";
	document.getElementById('porcentaje_extraext').innerHTML="2.1875%";
	document.getElementById('porcentaje_extranoc').innerHTML="1.75%";
	document.getElementById('porcentaje_extraextnoc').innerHTML="3.0625%";
	document.getElementById('porcentaje_extramixdiurna').innerHTML="1.5%";
	//document.getElementById('porcentaje_extraextmixdiurna').innerHTML="2.265%";
	document.getElementById('porcentaje_extraextmixdiurna').innerHTML="2.625%";
	document.getElementById('porcentaje_extramixnoc').innerHTML="1.75%";
	document.getElementById('porcentaje_extraextmixnoc').innerHTML="3.0625%";
}

function mostrar_porcentajes_domingo(){
	document.getElementById('porcentaje_extra').innerHTML="1.875%";
	document.getElementById('porcentaje_extraext').innerHTML="3.28125%";
	document.getElementById('porcentaje_extranoc').innerHTML="2.625%";
	document.getElementById('porcentaje_extraextnoc').innerHTML="4.59375%";
	document.getElementById('porcentaje_extramixdiurna').innerHTML="2.25%";
	document.getElementById('porcentaje_extraextmixdiurna').innerHTML="3.9375%";
	document.getElementById('porcentaje_extramixnoc').innerHTML="2.625%";
	document.getElementById('porcentaje_extraextmixnoc').innerHTML="4.59375%";	
}

function mostrar_porcentajes_nacional(){
	document.getElementById('porcentaje_extra').innerHTML="3.125%";
	document.getElementById('porcentaje_extraext').innerHTML="5.46875%";
	document.getElementById('porcentaje_extranoc').innerHTML="4.375%";
	document.getElementById('porcentaje_extraextnoc').innerHTML="7.65625%";
	document.getElementById('porcentaje_extramixdiurna').innerHTML="3.75%";
	document.getElementById('porcentaje_extraextmixdiurna').innerHTML="6.5625%";
	document.getElementById('porcentaje_extramixnoc').innerHTML="4.375%";
	document.getElementById('porcentaje_extraextmixnoc').innerHTML="7.65625%";
}