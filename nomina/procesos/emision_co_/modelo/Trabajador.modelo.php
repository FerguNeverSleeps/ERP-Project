<?php

require_once "conexion.php";
class TrabajadorModelo
{

    /**
     * @var string $tipo_trabajador
     */
    public $tipo_trabajador = null;

    /**
     * @var string $subtipo_trabajador
     */
    public $subtipo_trabajador = null;

    /**
     * @var string $alto_riesgo
     */
    public $alto_riesgo = null;

    /**
     * @var string $hora_gen
     */
    public $hora_gen = null;

    /**
     * @var string $tipo_documento
     */
    public $tipo_documento = null;

    /**
     * @var string $numero_documento
     */
    public $numero_documento = null;

    /**
     * @var string $primer_apellido
     */
    public $primer_apellido = null;

    /**
     * @var string $segundo_apellido
     */
    public $segundo_apellido = null;

    /**
     * @var string $primer_nombre
     */
    public $primer_nombre = null;

    /**
     * @var string $otros_nombres
     */
    public $otros_nombres = null;

    /**
     * @var string $pais_trabajo
     */
    public $pais_trabajo = null;

    /**
     * @var string $municipio_trabajo
     */
    public $municipio_trabajo = null;

    /**
     * @var string $direccion_trabajo
     */
    public $direccion_trabajo = null;

    /**
     * @var string $salario_integral
     */
    public $salario_integral = null;

    /**
     * @var string $tipo_contrato
     */
    public $tipo_contrato = null;

    /**
     * @var string $sueldo
     */
    public $sueldo = null;

    /**
     * @var string $codigo_trabajador
     */
    public $codigo_trabajador = null;

    /*
    public function __construct($tipo_trabajador,$subtipo_trabajador,$alto_riesgo,$hora_gen,$tipo_documento,$numero_documento,$primer_apellido,$segundo_apellido,$primer_nombre,$otros_nombres,$pais_trabajo,
    $municipio_trabajo,$direccion_trabajo,$salario_integral,$sueldo,$codigo_trabajador)
    {
        $this->tipo_trabajador    = $tipo_trabajador;
        $this->subtipo_trabajador = $subtipo_trabajador;
        $this->alto_riesgo        = $alto_riesgo;
        $this->hora_gen           = $hora_gen;
        $this->tipo_documento     = $tipo_documento;
        $this->numero_documento   = $numero_documento;
        $this->primer_apellido    = $primer_apellido;
        $this->segundo_apellido   = $segundo_apellido;
        $this->primer_nombre      = $primer_nombre;
        $this->otros_nombres      = $otros_nombres;
        $this->pais_trabajo       = $pais_trabajo;
        $this->municipio_trabajo  = $municipio_trabajo;
        $this->direccion_trabajo  = $direccion_trabajo;
        $this->salario_integral   = $salario_integral;
        $this->sueldo             = $sueldo;
        $this->codigo_trabajador  = $codigo_trabajador;
    
    }*/

    /**
     * @return string
     */
    public function getTipoTrabajador()
    {
      return $this->tipo_trabajador;
    }

    /**
     * @param string $tipo_trabajador
     * @return Trabajador
     */
    public function setTipoTrabajador($tipo_trabajador)
    {
      $this->tipo_trabajador = $tipo_trabajador;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubtipoTrabajador()
    {
      return $this->subtipo_trabajador;
    }

    /**
     * @param string $subtipo_trabajador
     * @return Trabajador
     */
    public function setSubtipoTrabajador($subtipo_trabajador)
    {
      $this->subtipo_trabajador = $subtipo_trabajador;
      return $this;
    }


    /**
     * @return string
     */
    public function getAltoRiesgo()
    {
      return $this->alto_riesgo;
    }

    /**
     * @param string $alto_riesgo
     * @return Trabajador
     */
    public function setAltoRiesgo($alto_riesgo)
    {
      $this->alto_riesgo = $alto_riesgo;
      return $this;
    }

    /**
     * @return string
     */
    public function getTipoDocumento()
    {
      return $this->tipo_documento;
    }

    /**
     * @param string $tipo_documento
     * @return Trabajador
     */
    public function setTipoDocumento($tipo_documento)
    {
      $this->tipo_documento = $tipo_documento;
      return $this;
    }

    /**
     * @return string
     */
    public function getNumeroDocumento()
    {
      return $this->numero_documento;
    }

    /**
     * @param string $numero_documento
     * @return Trabajador
     */
    public function setNumeroDocumento($numero_documento)
    {
      $this->numero_documento = $numero_documento;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrimerApellido()
    {
      return $this->primer_apellido;
    }

    /**
     * @param string $primer_apellido
     * @return Trabajador
     */
    public function setPrimerApellido($primer_apellido)
    {
      $this->primer_apellido = $primer_apellido;
      return $this;
    }

    /**
     * @return string
     */
    public function getSegundoApellido()
    {
      return $this->segundo_apellido;
    }

    /**
     * @param string $segundo_apellido
     * @return Trabajador
     */
    public function setSegundoApellido($segundo_apellido)
    {
      $this->segundo_apellido = $segundo_apellido;
      return $this;
    }

    /**
     * @return string
     */
    public function getPrimerNombre()
    {
      return $this->primer_nombre;
    }

    /**
     * @param string $primer_nombre
     * @return Trabajador
     */
    public function setPrimerNombre($primer_nombre)
    {
      $this->primer_nombre = $primer_nombre;
      return $this;
    }

    /**
     * @return string
     */
    public function getOtrosNombres()
    {
      return $this->otros_nombres;
    }

    /**
     * @param string $otros_nombres
     * @return Trabajador
     */
    public function setOtrosNombres($otros_nombres)
    {
      $this->otros_nombres = $otros_nombres;
      return $this;
    }

    /**
     * @return string
     */
    public function getPaisTrabajo()
    {
      return $this->pais_trabajo;
    }

    /**
     * @param string $pais_trabajo
     * @return Trabajador
     */
    public function setPaisTrabajo($pais_trabajo)
    {
      $this->pais_trabajo = $pais_trabajo;
      return $this;
    }

    /**
     * @return string
     */
    public function getMunicipioTrabajo()
    {
      return $this->municipio_trabajo;
    }

    /**
     * @param string $municipio_trabajo
     * @return Trabajador
     */
    public function setMunicipioTrabajo($municipio_trabajo)
    {
      $this->municipio_trabajo = $municipio_trabajo;
      return $this;
    }

    /**
     * @return string
     */
    public function getDireccionTrabajo()
    {
      return $this->direccion_trabajo;
    }

    /**
     * @param string $direccion_trabajo
     * @return Trabajador
     */
    public function setDireccionTrabajo($direccion_trabajo)
    {
      $this->direccion_trabajo = $direccion_trabajo;
      return $this;
    }

    /**
     * @return string
     */
    public function getSalarioIntegral()
    {
      return $this->salario_integral;
    }

    /**
     * @param string $salario_integral
     * @return Trabajador
     */
    public function setSalarioIntegral($salario_integral)
    {
      $this->salario_integral = $salario_integral;
      return $this;
    }

    /**
     * @return string
     */
    public function getTipoContrato()
    {
      return $this->tipo_contrato;
    }

    /**
     * @param string $tipo_contrato
     * @return Trabajador
     */
    public function setTipoContrato($tipo_contrato)
    {
      $this->tipo_contrato = $tipo_contrato;
      return $this;
    }

    /**
     * @return string
     */
    public function getSueldo()
    {
      return $this->sueldo;
    }

    /**
     * @param string $sueldo
     * @return Trabajador
     */
    public function setSueldo($sueldo)
    {
      $this->sueldo = $sueldo;
      return $this;
    }

    /**
     * @return string
     */
    public function getCodigoTrabajador()
    {
      return $this->codigo_trabajador;
    }

    /**
     * @param string $codigo_trabajador
     * @return Trabajador
     */
    public function setCodigoTrabajador($codigo_trabajador)
    {
      $this->codigo_trabajador = $codigo_trabajador;
      return $this;
    }
    static public function GetDatosTrabajador( $tipnom ) 
    {      
      $select_trabajdor="SELECT
          p.*,
          b.des_ban descripcion_banco
        FROM
          nom_nominas_pago np
          LEFT JOIN nom_movimientos_nomina mn ON mn.codnom = np.codnom 
          AND mn.tipnom = np.codtip
          LEFT JOIN nompersonal p ON p.ficha = mn.ficha 
          LEFT JOIN nombancos b ON p.codbancob = b.cod_ban
        WHERE
          mn.tipnom = :tipnom 
        GROUP BY
          p.ficha;";

      $conex = Conexion::conectar(null);
      
      $select = $conex ->prepare( $select_trabajdor );
      $select -> bindParam(":tipnom" , $tipnom, PDO::PARAM_STR);
      $select -> execute();
      return $select ->fetchAll(PDO::FETCH_ASSOC);

    }
    static public function GetDevengados( $datos )
    { 		 
      $conex = Conexion::conectar();
      $select =  $conex->prepare("SELECT COALESCE(SUM(monto),0) as monto FROM nom_movimientos_nomina WHERE  ficha = :ficha AND codnom = :codnom AND tipnom = :tipnom AND tipcon  = 'A' ");
      $select -> bindParam(":ficha" , $datos["ficha"], PDO::PARAM_STR);
      $select -> bindParam(":codnom" , $datos["codnom"], PDO::PARAM_STR);
      $select -> bindParam(":tipnom" , $datos["tipnom"], PDO::PARAM_STR);

      $select -> execute();
      $return = $select -> fetchAll( PDO::FETCH_ASSOC );
      return $return[0];
    }
    static public function GetDatosConceptosDevengos( )
    { 		 
      $conex = Conexion::conectar(); 
      $select =  $conex->prepare("SELECT * from emision_col_configuracion where tipo = '2' AND conceptos is not null and conceptos != ''; ");

      $select -> execute();
      $return = $select -> fetchAll( PDO::FETCH_ASSOC );
      return $return;
    }
    static public function GetDatosConceptosDeducciones( )
    { 		 
      $conex = Conexion::conectar(); 
      $select =  $conex->prepare("SELECT * from emision_col_configuracion where tipo = '1' AND conceptos is not null and conceptos != ''; ");

      $select -> execute();
      $return = $select -> fetchAll( PDO::FETCH_ASSOC );
      return $return;
    }
    static public function GetDeducciones( $datos )
    {
      $conex = Conexion::conectar();
      $select =  $conex->prepare("SELECT COALESCE(SUM(monto),0) as monto FROM nom_movimientos_nomina WHERE  ficha = :ficha AND codnom = :codnom AND tipnom = :tipnom AND tipcon  = 'D' ");
      $select -> bindParam(":ficha" , $datos["ficha"], PDO::PARAM_STR);
      $select -> bindParam(":codnom" , $datos["codnom"], PDO::PARAM_STR);
      $select -> bindParam(":tipnom" , $datos["tipnom"], PDO::PARAM_STR);

      $select -> execute();
      $return = $select -> fetchAll( PDO::FETCH_ASSOC );
      return $return[0];
    }
    static public function GetMontoConcepto( $datos, $tipcon )
    { 		 
      $conex = Conexion::conectar();
      $select =  $conex->prepare("SELECT COALESCE(SUM(monto),0) as monto, valor FROM nom_movimientos_nomina WHERE  ficha = :ficha AND codcon in (:concepto) AND codnom = :codnom AND tipnom = :tipnom AND tipcon  = :tipcon; ");
      $select -> bindParam(":ficha" , $datos["ficha"] , PDO::PARAM_STR);
      $select -> bindParam(":codnom" , $datos["codnom"] , PDO::PARAM_STR);
      $select -> bindParam(":tipnom" , $datos["tipnom"] , PDO::PARAM_STR);
      $select -> bindParam(":concepto" , $datos["concepto"] , PDO::PARAM_STR);
      $select -> bindParam(":tipcon" , $tipcon , PDO::PARAM_STR);

      $select -> execute();
      $return = $select -> fetchAll( PDO::FETCH_ASSOC );
      return $return[0];
    }
}
