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
      "afc": "10000.00",
      "anticiposNom": [
         {
            "extrasNom": null,
            "montoanticipo": "10000.00"
         },
         {
            "extrasNom": null,
            "montoanticipo": "10000.00"
         }
      ],
      "cooperativa": "10000.00",
      "deuda": "10000.00",
      "educacion": "10000.00",
      "extrasNom" : null,
      "embargoFiscal": "10000.00",
      "fondosPensiones": [
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         }
      ],
      "fondosSP": [
         {
            "deduccionSP": "10000.00",
            "deduccionSub": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00",
            "porcentajeSub": "10.00"
         }
      ],
      "libranzas": [
         {
            "deduccion": "10000.00",
            "descripcion": "Descripcion General de Libranza",
            "extrasNom" : null
         },
         {
            "deduccion": "10000.00",
            "descripcion": "Descripcion General de Libranza",
            "extrasNom" : null
         }
      ],
      "otrasDeducciones": [
         {
            "extrasNom" : null,
            "montootraDeduccion": "10000.00"
         },
         {
            "extrasNom" : null,
            "montootraDeduccion": "10000.00"
         }
      ],
      "pagosTerceros": [
         {
            "montopagotercero": "10000.00",
            "extrasNom" : null
         },
         {
            "montopagotercero": "10000.00",
            "extrasNom" : null
         }
      ],
      "pensionVoluntaria": "10000.00",
      "planComplementarios": "10000.00",
      "reintegro": "10000.00",
      "retencionFuente": "10000.00",
      "salud": [
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         }
      ],
      "sanciones": [
         {
            "extrasNom" : null,
            "sancionPublic": "10000.00",
            "sancionPriv": "10000.00"
         },
         {
            "extrasNom" : null,
            "sancionPublic": "10000.00",
            "sancionPriv": "10000.00"
         }
      ],
      "sindicatos": [
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         },
         {
            "deduccion": "10000.00",
            "extrasNom" : null,
            "porcentaje": "10.00"
         }
      ]
   },
   "devengados": {
      "anticiposNom": [
         {
            "extrasNom" : null,
            "montoanticipo": "10000.00"
         },
         {
            "extrasNom" : null,
            "montoanticipo": "10000.00"
         }
      ],
      "auxilios": [
         {
            "auxilioNS": "10000.00",
            "auxilioS": "10000.00",
            "extrasNom" : null
         },
         {
            "auxilioNS": "10000.00",
            "auxilioS": "10000.00",
            "extrasNom" : null
         }
      ],
      "apoyoSost": "10000.00",
      "basico": [
         {
            "diasTrabajados": "15",
            "extrasNom" : null,
            "sueldoTrabajado": "500000.00"
         }
      ],
      "bonificaciones": [
         {
            "bonificacionNS": "10000.00",
            "bonificacionS": "10000.00",
            "extrasNom" : null
         },
         {
            "bonificacionNS": "10000.00",
            "bonificacionS": "10000.00",
            "extrasNom" : null
         }
      ],
      "bonifRetiro": "10000.00",
      "bonoEPCTVs": [
         {
            "pagoAlimentacionNS": "10000.00",
            "pagoAlimentacionS": "10000.00",
            "extrasNom" : null,
            "pagoNS": "10000.00",
            "pagoS": "10000.00"
         },
         {
            "pagoAlimentacionNS": "10000.00",
            "pagoAlimentacionS": "10000.00",
            "extrasNom" : null,
            "pagoNS": "10000.00",
            "pagoS": "10000.00"
         }
      ],
      "cesantias": [
         {
            "extrasNom" : null,
            "pago": "10000.00",
            "pagoIntereses": "10000.00",
            "porcentaje": "10.00"
         }
      ],
      "comisiones": [
         {
            "extrasNom" : null,
            "montocomision": "10000.00"
         },
         {
            "extrasNom" : null,
            "montocomision": "10000.00"
         }
      ],
      "compensaciones": [
         {
            "compensacionE": "10000.00",
            "compensacionO": "10000.00",
            "extrasNom" : null
         },
         {
            "compensacionE": "10000.00",
            "compensacionO": "10000.00",
            "extrasNom" : null
         }
      ],
      "dotacion": "10000.00",
      "extrasNom" : null,
      "horasExtras": [
         {
            "cantidad": "10000.00",
            "extrasNom" : null,
            "horaInicio": "2021-03-02 12:00:00",
            "horaFin": "2021-03-02 12:00:00",
            "pago": "10000.00",
            "porcentaje": "25.00",
            "tipoHorasExtra": "0"
         },
         {
            "cantidad": "10000.00",
            "extrasNom" : null,
            "horaInicio": "2021-03-02 12:00:00",
            "horaFin": "2021-03-02 12:00:00",
            "pago": "10000.00",
            "porcentaje": "25.00",
            "tipoHorasExtra": "1"
         }
      ],
      "huelgasLegales": [
         {
            "cantidad": "30",
            "extrasNom" : null,
            "fechaInicio": "2021-03-15",
            "fechaFin": "2021-03-15"
         },
         {
            "cantidad": "30",
            "extrasNom" : null,
            "fechaInicio": "2021-03-15",
            "fechaFin": "2021-03-15"
         }
      ],
      "indemnizacion": "10000.00",
      "incapacidades": [
         {
            "cantidad": "30",
            "extrasNom" : null,
            "fechaInicio": "2021-03-15",
            "fechaFin": "2021-03-15",
            "pago": "10000.00",
            "tipo": "2"
         },
         {
            "cantidad": "30",
            "extrasNom" : null,
            "fechaInicio": "2021-03-15",
            "fechaFin": "2021-03-15",
            "pago": "10000.00",
            "tipo": "2"
         }
      ],
      "licencias": {
         "extrasNom" : null,
         "licenciaMP": [
            {
               "cantidad": "30",
               "extrasNom": null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            },
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            }
         ],
         "licenciaNR": [
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            },
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            }
         ],
         "licenciaR": [
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            },
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            }
         ]
      },
      "otrosConceptos": [
         {
            "conceptoNS": "10000.00",
            "conceptoS": "10000.00",
            "descripcionConcepto": "Descipcion Generica",
            "extrasNom" : null
         },
         {
            "conceptoNS": "10000.00",
            "conceptoS": "10000.00",
            "descripcionConcepto": "Descipcion Generica",
            "extrasNom" : null
         }
      ],
      "pagosTerceros": [
         {
            "montopagotercero": "10000.00",
            "extrasNom" : null
         },
         {
            "montopagotercero": "10000.00",
            "extrasNom" : null
         }
      ],
      "primas": [
         {
            "cantidad": "30",
            "extrasNom" : null,
            "pago": "10000.00",
            "pagoNS": "10000.00"
         }
      ],
      "reintegro": "10000.00",
      "teletrabajo": "10000.00",
      "transporte": [
         {
            "auxilioTransporte": "10000.00",
            "extrasNom" : null,
            "viaticoManuAlojS": "10000.00",
            "viaticoManuAlojNS": "10000.00"
         },
         {
            "auxilioTransporte": "10000.00",
            "extrasNom" : null,
            "viaticoManuAlojS": "10000.00",
            "viaticoManuAlojNS": "10000.00"
         }
      ],
      "vacaciones": {
         "extrasNom" : null,
         "vacacionesComunes": [
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            },
            {
               "cantidad": "30",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            }
         ],
         "vacacionesCompensadas": [
            {
               "cantidad": "20",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            },
            {
               "cantidad": "20",
               "extrasNom" : null,
               "fechaInicio": "2021-03-15",
               "fechaFin": "2021-03-15",
               "pago": "10000.00"
            }
         ]
      }
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
   "novedad": "0",
   "novedadCUNE": "d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e",
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
   "tipoDocumentoNom": "102",
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


//RESPONSE - EJEMPLO
/*
{
    "codigo": 99,
    "mensaje": "Documento con errores en campos mandatorios.",
    "resultado": "Error",
    "consecutivoDocumento": null,
    "cune": "593b57b1b94a55cb4d6b888672ae0ac31e771d35e9c15d62000b002bce90b7e596374d6b293b69be79ec28ba0cbbce5d",
    "trackId": "d8c4dbcf-b537-40a7-bdd9-f979e710ebf5",
    "reglasNotificacionesTFHKA": null,
    "reglasNotificacionesDIAN": [],
    "reglasRechazoTFHKA": null,
    "reglasRechazoDIAN": [
        "Regla: 90, Rechazo: Documento procesado anteriormente"
    ],
    "nitEmpleador": "900390126",
    "nitEmpleado": "900390126",
    "idSoftware": null,
    "qr": "NumNIE: FSNE1\r\nFecNIE: 2021-03-02\r\nHorNIE: 12:00:00-05:00\r\nNitNIE: 900390126\r\nDocEmp: 900390126\r\nValDev: 500000.00\r\nValDed: 40000.00\r\nValTol: 410000.00\r\nCUNE: 593b57b1b94a55cb4d6b888672ae0ac31e771d35e9c15d62000b002bce90b7e596374d6b293b69be79ec28ba0cbbce5d\r\nhttps://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=593b57b1b94a55cb4d6b888672ae0ac31e771d35e9c15d62000b002bce90b7e596374d6b293b69be79ec28ba0cbbce5d",
    "esvalidoDIAN": false,
    "xml": null
}
*/

?>