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

function guardar(valor, accion)
{
	var nombre     = "actual" + valor;

	var codigo     = document.getElementById('codigo');
	var desde1     = document.getElementById('desde');
	var hasta1     = document.getElementById('hasta');
	var resultado1 = document.getElementById('resultado');
	var tabla      = document.getElementById('table_datatable');	// tabla_detalles
	var cuerpo     = document.getElementById('tbody');              // tabla_cuerpo
	var actual     = document.getElementById(nombre);
	
	var url = "mantenimiento_dinamico.php?accion=" + accion + "&codigo=" + codigo.value;

	switch(accion){
		case "agregar":
			url = url + "&desde=" + desde1.value + "&hasta=" + hasta1.value + "&resultado=" + resultado1.value;
		break
		case "editar":			
			var hastaviejo=document.getElementById('hastaviejo');

			url = url + "&desde=" + desde1.value + "&hasta=" + hasta1.value + "&resultado=" + resultado1.value + "&hastaviejo=" + hastaviejo.value
		break
	}

	contenido=abrirAjax();
	contenido.open("GET",url, true);

	contenido.onreadystatechange=function() 
	{
		if (contenido.readyState==4)
		{
			var temp = contenido.responseText;
			
			if(temp==0)
			{ 
				var fila = document.createElement("tr");
				
				var desde = document.createElement("td");
				desde.innerHTML = desde1.value;

				var hasta = document.createElement("td");
				hasta.innerHTML = hasta1.value;

				var resultado = document.createElement("td");
				resultado.innerHTML = resultado1.value;

				var agregar = document.createElement("td");
				agregar.innerHTML = "<a href=\"javascript:agregar_despues("+valor+")\" title=\"Agregar despues de este registro\"><img src=\"../imagenes/add.gif\" width=\"16\" height=\"16\"></a>";
				
				var editar = document.createElement("td");
				editar.innerHTML = "<a href=\"javascript:editar()\" title=\"Editar\"><img src=\"../imagenes/edit.gif\" width=\"16\" height=\"16\"></a>";
				
				var eliminar = document.createElement("td");
				eliminar.innerHTML = "<a href=\"javascript:eliminar()\"><img title=\"Eliminar\" src=\"../imagenes/delete.gif\" width=\"16\" height=\"16\"></a>";
				
				fila.appendChild(desde);
				fila.appendChild(hasta);
				fila.appendChild(resultado);
				fila.appendChild(agregar);
				fila.appendChild(editar);
				fila.appendChild(eliminar);
				cuerpo.insertBefore(fila, actual);
				cuerpo.removeChild(actual);
				fila.id=nombre;
				document.location.href = "baremos_detalles.php?codigo=" + codigo.value;				
			}
			else
			{
				alert("El registro ya se encuentra");
			}			
		}
	}
	contenido.send(null);
}

function agregar_despues(valor)
{
	var cuerpo    = document.getElementById("tbody"); // tabla_cuerpo
	var fila      = document.createElement("tr");
	var siguiente = parseInt(valor) + 1;
	fila.id = 'actual' + siguiente;
 
	var desde = document.createElement("td");
	desde.innerHTML = "<input  type=\"text\" name=\"desde\" id=\"desde\">";

	var hasta = document.createElement("td");
	hasta.innerHTML = "<input type=\"text\" name=\"hasta\" id=\"hasta\">";

	var resultado = document.createElement("td");
	resultado.innerHTML = "<input type=\"text\" name=\"monto\" id=\"resultado\" onblur=\"javascript:guardar('"+siguiente+"','agregar')\">";
	
	var columna1 = document.createElement("td");
	var columna2 = document.createElement("td");
	var columna3 = document.createElement("td");

	fila.appendChild(desde);
	fila.appendChild(hasta);
	fila.appendChild(resultado);
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);

	cuerpo.appendChild(fila);
}

function eliminar(codigo,hasta)
{
	if(confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
	{
		contenido=abrirAjax();
		contenido.open("GET","mantenimiento_dinamico.php?accion=eliminar&codigo="+codigo+"&hasta="+hasta,true);
		contenido.onreadystatechange=function() 
		{
			if (contenido.readyState==4)
			{
				
				document.location.href="baremos_detalles.php?codigo="+codigo;	
			}
		
		}
		contenido.send(null);
	}
}

function editar(valor,codigo,hastav)
{
	var nombre = "actual" + valor
	var tabla  = document.getElementById('table_datatable') // tabla_detalles
	var cuerpo = document.getElementById('tbody') // tabla_cuerpo
	var actual = document.getElementById(nombre)
	contenido=abrirAjax()
	contenido.open("GET","mantenimiento_dinamico.php?accion=consultar&codigo="+codigo+"&hasta="+hastav, true)
	contenido.onreadystatechange=function() 
	{
		if (contenido.readyState==4)
		{
				var temp=contenido.responseText
			
				var retorno=temp.split('-')
				
				var fila=document.createElement("tr")
				
				var desde=document.createElement("td")
				desde.innerHTML="<input value=\""+retorno[0]+"\" type=\"text\" name=\"desde\" id=\"desde\">"
				
				var hasta=document.createElement("td")
				hasta.innerHTML="<input type=\"text\" value=\""+retorno[1]+"\" name=\"hasta\" id=\"hasta\">"
				
				var resultado=document.createElement("td")
				resultado.innerHTML="<input type=\"text\" name=\"monto\" value=\""+retorno[2]+"\" id=\"resultado\" onblur=\"javascript:guardar('"+valor+"','editar')\">"
				
				var columna1 = document.createElement("td");
				var columna2 = document.createElement("td");
				var columna3 = document.createElement("td");

				columna1.innerHTML="<input type=\"hidden\" value=\""+hastav+"\" name=\"hastaviejo\" id=\"hastaviejo\">"

				fila.appendChild(desde)
				fila.appendChild(hasta)
				fila.appendChild(resultado)
				fila.appendChild(columna1)
				fila.appendChild(columna2)
				fila.appendChild(columna3)

				cuerpo.insertBefore(fila,actual)
				cuerpo.removeChild(actual)
				fila.id=nombre
		}
	}
	contenido.send(null);
}