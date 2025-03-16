<?php 
session_start();
ob_start();
?>
<script>


</script>
<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
        include("func_bd.php");	
?>
<body class="page-full-width"  marginheight="0">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
					        	Agregar Proyectos
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='proyectos_list.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="1">
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="numProyecto">Num. Proyecto:</label></div>
									<div class="col-md-6"><input name="numProyecto" type="text" id="numProyecto"  class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcionCorta">Descripci&oacute;n Corta:</label></div>
									<div class="col-md-6"><input name="descripcionCorta" type="text" id="descripcionCorta"  class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcionLarga">Descripci&oacute;n Larga:</label></div>
									<div class="col-md-6"><input name="descripcionLarga" type="text" id="descripcionLarga"  class="form-control"></div>
								</div>
                                                                <div class="row">
									&nbsp;
								</div>
                                                                <div class="row">
									<div class="col-md-3 text-right"><label for="descripcionLarga">Dispositivo (Reloj):</label></div>
									<div class="col-md-6">
                                                                            <select name="dispositivo" id="dispositivo" class="form-control select2" >
                                                                                <?php 
                                                                                $query="select id_dispositivo, cod_dispositivo, nombre, ubicacion from reloj_info";
                                                                                $result=sql_ejecutar($query);
                                                                              //ciclo para mostrar los datos
                                                                                while ($row = fetch_array($result))
                                                                                {     
                                                                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                                                                   if($row[id_dispositivo]==$dispositivo)
                                                                                   { 
                                                                                ?>
                                                                                  <option value="<?php echo $row[id_dispositivo];?>" selected > <?php echo $row[cod_dispositivo]." - ".$row[nombre];?> </option>
                                                                                  <?php 
                                                                                   }
                                                                                   else // opcion de agregar
                                                                                   { 
                                                                                  ?>
                                                                                   <option value="<?php echo $row[id_dispositivo];?>"><?php echo $row[cod_dispositivo]." - ".$row[nombre];?></option>
                                                                                    <?php 
                                                                                   } 
                                                                                }//fin del ciclo while
                                                                                    ?>
                                                                          </select>
                                                                        </div>
								</div>
								<div class="row">
									&nbsp;
								</div>

<div class="row">
<div class="col-md-3 text-right"><label>Coordenadas:</label></div>
<div class="col-md-6">
<div id="map" style="height: 400px;"></div>
</div>
</div>

<div class="row">&nbsp;</div>

<div class="row">
<div class="col-md-3 text-right"><label for="lat">Latitud:</label></div>
<div class="col-md-6"><input name="lat" type="text" id="lat" class="form-control"></div>
</div>

<div class="row">&nbsp;</div>

<div class="row">
<div class="col-md-3 text-right"><label for="lng">Longitud:</label></div>
<div class="col-md-6"><input name="lng" type="text" id="lng" class="form-control"></div>
</div>

<div class="row">&nbsp;</div>

								<div class="row">
									<div class="col-md-offset-4 col-md-1"><?php boton_metronic('ok','Enviar();',2) ?></div>
									<div class="col-md-2"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	include ("../footer4.php");
 ?>
 <script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>


<script type="text/javascript">
//const apiKey = 'AIzaSyA_64T-H0k-l999aAX5UNZlMghLXFaeNPA';
const apiKey = 'AIzaSyAO0cUc_zWHIaLSohvheWuKcbmG7H5RMlY';
const script = document.createElement('script');
script.src = `https://maps.googleapis.com/maps/api/js?v=weekly&key=${apiKey}&callback=initMap&language=es`;
script.defer = true;

let map;
let marker;

function obtenerCoordenadas(evt) {
  const lat = parseFloat(evt.latLng.lat().toFixed(7));
  const lng = parseFloat(evt.latLng.lng().toFixed(7));

  return { lat, lng };
}

function actualizarInputsLatitudLongitud(lat, lng) {
  document.getElementById('lat').value = lat;
  document.getElementById('lng').value = lng;
}

function actualizarCoordenadas(evt) {
  const { lat, lng } = obtenerCoordenadas(evt);

  actualizarInputsLatitudLongitud(lat, lng);
}

function iniciarMarkerSiNoExiste(position) {
  if (map && !marker) {
    marker = new google.maps.Marker({
      map,
      draggable: true,
      animation: google.maps.Animation.DROP,
      position,
    });
  
    marker.addListener('dragend', actualizarCoordenadas);
    marker.addListener('click', actualizarCoordenadas);
  }
}

function removerMarker() {
  if (marker) {
    marker.setMap(null);
    marker = null;
  }
}

function actualizarCoordenadasEnMapa() {
  let lat = document.getElementById('lat').value.trim();
  let lng = document.getElementById('lng').value.trim();

  lat = parseFloat(lat); lng = parseFloat(lng);

  if (isNaN(lat) || isNaN(lng)) {
    removerMarker();
    return false;
  }

  const coordenadas = { lat, lng };

  iniciarMarkerSiNoExiste(coordenadas);
  marker.setPosition(coordenadas);
  map.setCenter(coordenadas);
}

window.initMap = function() {
  const coordenadas = { lat: 9.0060184, lng: -79.5041212 };

  map = new google.maps.Map(document.getElementById('map'), {
    center: coordenadas,
    zoom: 14,
    zoomControl: true,
    zoomControlOptions: {
      position: google.maps.ControlPosition.RIGHT_CENTER,
    },
    mapTypeControl: false,
    streetViewControl: false,
    rotateControl: false,
    fullscreenControl: false,
    styles: [
      {
        featureType: 'transit',
        elementType: 'labels',
        stylers: [
          {
            visibility: 'off',
          },
        ],
      },
      {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [
          {
            visibility: 'off',
          },
        ],
      },
    ],
  });

  map.addListener('click', function(evt) {
    const { lat, lng } = obtenerCoordenadas(evt);

    const coordenadas = { lat, lng };

    actualizarInputsLatitudLongitud(lat, lng);
    iniciarMarkerSiNoExiste(marker);

    marker.setPosition(coordenadas);
  });
};

function handlerActualizarCoordenadas (evt) {
  evt.preventDefault();

  actualizarCoordenadasEnMapa();
}

document.getElementById('lat').addEventListener('blur', handlerActualizarCoordenadas);
document.getElementById('lng').addEventListener('blur', handlerActualizarCoordenadas);

document.head.appendChild(script);

function Enviar(){
	numProyecto      = $("#numProyecto").val();
	descripcionCorta = $("#descripcionCorta").val();
	descripcionLarga = $("#descripcionLarga").val();
	idDispositivo = $("#dispositivo").val();
        lat = $('#lat').val();
        lng = $('#lng').val();
	sw = 1;

	if (numProyecto =="") {
		alert("Ingrese el numero de proyecto");
		sw=0;
	}
	else{
		if (descripcionCorta =="") {
			alert("Ingrese la descripción Corta");
			sw=0;
		}
		else{
			$.get("proyectos_buscar.php",{numProyecto:numProyecto},function(res){
				console.log(res);
				if(res > 0)
				{
					alert("Numero de proyecto repetido");
					sw=0;
				}
				if (sw){
					$.get("proyectos_agregar.php",
					{numProyecto:numProyecto,descripcionCorta:descripcionCorta,descripcionLarga:descripcionLarga,idDispositivo:idDispositivo, lat: lat, lng: lng },function(){
						alert("Proyecto Agregado exitosamente");
						location.href="proyectos_list.php";
					});
				}
			});
		}
	}
}
 </script>
</body>
</html>
