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
   "deducciones": null,
   "devengados": null,
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
   "notas": [
      {
         "descripcion": "Nota de prueba 1",
         "extrasNom" : null
      },
      {
         "descripcion": "Nota de prueba 1",
         "extrasNom" : null
      }
   ],
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
   "periodos": null,
   "rangoNumeracionNom": "FSNE-1",
   "redondeo": "0.00",
   "tipoDocumentoNom": "103",
   "tipoMonedaNom": "COP",
   "tipoNota": "2",
   "totalComprobante": "410000.00",
   "totalDeducciones": "40000.00",
   "totalDevengados": "500000.00",
   "trm": "3000.00",
   "trabajador": null
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