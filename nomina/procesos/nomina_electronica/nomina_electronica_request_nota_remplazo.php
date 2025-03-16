<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://demo-nomina-rest.thefactoryhka.com.co/Enviar',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
  "tokenEnterprise": "0d2becf57f6619cf7460c3f5d5a1b1a292da4fd0",
  "tokenPassword": "0c3ae232d287f0210d68327e42723df6ef3e811c",
  "idSoftware": "123456",
  "nitEmpleador": "900390126",
  "objNomina": {
   "consecutivoDocumentoNom": "FSNE1",
   "deducciones": {
      "fondosPensiones": [
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         }
      ],
      "salud": [
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         }
      ]
   },
   "devengados": {
      "basico": [
         {
            "diasTrabajados": "15",
            "extrasNom" : null,
            "sueldoTrabajado": "500000.00"
         }
      ]
   },
   "documentosReferenciadosNom": [
      {
         "cunePred": "d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e",
         "extrasNom" : null,
         "fechaGenPred": "2021-03-04",
         "numeroPred": "DSNE1"
      }
   ],
   "extrasNom": null,
   "fechaEmisionNom": "2021-03-02 12:00:00",
   "lugarGeneracionXML": {
      "departamentoEstado": "11",
      "extrasNom" : null,
      "idioma": "es",
      "municipioCiudad": "11001",
      "pais": "CO"
   },
   "pagos": [
      {
         "extrasNom" : null,
         "fechasPagos": [
            {
               "extrasNom" : null,
               "fechapagonomina": "2021-03-15"
            },
            {
               "extrasNom" : null,
               "fechapagonomina": "2021-03-15"
            }
         ],
         "metodoDePago": "1",
         "medioPago": "ZZZ",
         "nombreBanco": "Nombre del Banco",
         "tipoCuenta": "Ahorro",
         "numeroCuenta": "123456789"
      }
   ],
   "periodoNomina": "4",
   "periodos": [
      {
         "extrasNom" : null,
         "fechaIngreso": "2021-03-02",
         "fechaLiquidacionInicio": "2021-03-03",
         "fechaLiquidacionFin": "2021-03-04",
         "fechaRetiro": "2021-03-02",
         "tiempoLaborado": "3"
      }
   ],
   "rangoNumeracionNom": "FSNE-1",
   "redondeo": "0.00",
   "tipoDocumentoNom": "103",
   "tipoMonedaNom": "COP",
   "tipoNota": "1",
   "totalComprobante": "410000.00",
   "totalDeducciones": "40000.00",
   "totalDevengados": "500000.00",
   "trm": "3000.00",
   "trabajador": {
      "altoRiesgoPension": "0",
      "codigoTrabajador": "A527",
      "email": "asgarcia@thefactoryhka.com",
      "extrasNom" : null,
      "lugarTrabajoDepartamentoEstado": "11",
      "lugarTrabajoDireccion": "Direccion de prueba",
      "lugarTrabajoMunicipioCiudad": "11001",
      "lugarTrabajoPais": "CO",
      "numeroDocumento": "900390126",
      "otrosNombres": null,
      "primerApellido": "Garcia",
      "primerNombre": "Anderson",
      "salarioIntegral": "0",
      "segundoApellido": "rojas",
      "subTipoTrabajador": "00",
      "sueldo": "500000.00",
      "tipoContrato": "1",
      "tipoIdentificacion": "31",
      "tipoTrabajador": "01"
   }
  }
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;


?>