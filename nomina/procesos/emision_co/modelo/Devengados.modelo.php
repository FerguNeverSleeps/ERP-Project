<?php

class Devengados
{

    /**
     * @var array $dias_trabajados
     */
    public $dias_trabajados = null;
    /**
     * @var array $sueldo_trabajado
     */
    public $sueldo_trabajado = null;
    
    public function __construct($dias_trabajados = "", $sueldo_trabajado = "")
    {
        $this->dias_trabajados    = $dias_trabajados =="" ? $dias_trabajados : "";
        $this->sueldo_trabajado    = $sueldo_trabajado =="" ? $sueldo_trabajado : "";
    }
    /**
     * @return string
     */
    public function getDiasTrabajados()
    {
      return $this->dias_trabajados;
    }

    /**
     * @param string $dias_trabajados
     * @return Devengados
     */
    public function setDiasTrabajados($dias_trabajados)
    {
      $this->dias_trabajados = $dias_trabajados;
      return $this;
    }

    /**
     * @return string
     */
    public function getSueldoTrabajado()
    {
      return $this->dias_trabajados;
    }

    /**
     * @param string $sueldo_trabajado
     * @return Devengados
     */
    public function setSueldoTrabajado($sueldo_trabajado)
    {
      $this->sueldo_trabajado = $sueldo_trabajado;
      return $this;
    }

    function setTransporte($datos)
    {
        /*$Transporte = new Transporte();
        
        $Transporte->setAuxilio($datos["auxilio"]);
        $Transporte->setAlojS($datos["aloj_s"]);
        $Transporte->setAlojNS($datos["aloj_ns"]);
        */
        return array(
            "auxilio"  =>$datos["auxilio"],
            "aloj_s" =>$datos["aloj_s"],
            "aloj_ns" =>$datos["aloj_ns"]
        );
    }
    public function setVacacionesComunes($datos)
    {
        /*$Vacaciones = new Vacaciones();
        
        $Vacaciones->setPago($datos["pago"]);
        $Vacaciones->setFechaInicio($datos["fecha_inicio"]);
        $Vacaciones->setFechaFin($datos["fecha_fin"]);
        $Vacaciones->setCantidad($datos["cantidad"]);
        */
        
        return array(
            "pago"  => $datos["pago"],
            "fecha_inicio" => $datos["fecha_inicio"],
            "fecha_fin" => $datos["fecha_fin"],
            "cantidad" => $datos["cantidad"]
        );
    }
    function setVacacionesCompensadas($datos)
    {
        /*$Vacaciones = new Vacaciones();
        
        $Vacaciones->setPago($datos["pago"]);
        $Vacaciones->setCantidad($datos["fecha_inicio"]);*/
        
        return array(
            "pago"  => $datos["pago"],
            "cantidad" => $datos["fecha_inicio"]
        );
    }
    function setPrimas($datos)
    {
        /*$Primas = new Primas();
        
        $Primas->setPago($datos["pago"]);
        $Primas->setPagoNS($datos["pago_ns"]);
        $Primas->setCantidad($datos["fecha_inicio"]);*/
        
        return array(
            "pago"  =>$datos["pago"],
            "pago_ns"  =>$datos["pago_ns"],
            "cantidad" =>$datos["fecha_inicio"]
        );
    }
    function setCesantias($datos)
    {
        /*$Cesantias = new Cesantias();
        
        $Cesantias->setPago($datos["pago"]);
        $Cesantias->setPorcentaje($datos["porcentaje"]);
        $Cesantias->setPagoIntereses($datos["pago_intereses"]);*/
        
        return array(
            "pago"  => $datos["pago"],
            "porcentaje"  => $datos["porcentaje"],
            "pago_intereses" => $datos["pago_intereses"]
        );
    }
    function setVacaciones($datos)
    {
        /* $Vacaciones = new Vacaciones();
        
        $Vacaciones->setVacacionesComunes($datos["comunes"]);
        $Vacaciones->setVacacionesCompensadas($datos["compensadas"]);*/
        
        return array(
            "comunes"  => $this->setVacacionesComunes($datos["comunes"]),
            "compensadas" => $this->setVacacionesCompensadas($datos["compensadas"])
        );
    }
    function setIncapacidades($datos)
    {
        return array(
            "pago" => $datos["pago"],
            "tipo" => $datos["tipo"],
            "cantidad" => $datos["cantidad"]
        );
    }
    public function setMaternidadPaternidad($datos)
    {
        $MaternidadPaternidad = new MaternidadPaternidad();
        $MaternidadPaternidad->setPago($datos["pago"]);
        $MaternidadPaternidad->setFechaInicio($datos["fecha_inicio"]);
        $MaternidadPaternidad->setFechaFin($datos["fecha_fin"]);
        $MaternidadPaternidad->setCantidad($datos["cantidad"]);
        
        return array(
            "pago"  => $MaternidadPaternidad->getPago(),
            "fecha_inicio" => $MaternidadPaternidad->getFechaInicio(),
            "fecha_fin" => $MaternidadPaternidad->getFechaFin(),
            "cantidad" => $MaternidadPaternidad->getCantidad()
        );

    }
    public function setRemuneradas($datos)
    {
        $Remuneradas = new Remuneradas();
        $Remuneradas->setPago($datos["pago"]);
        $Remuneradas->setFechaInicio($datos["fecha_inicio"]);
        $Remuneradas->setFechaFin($datos["fecha_fin"]);
        $Remuneradas->setCantidad($datos["cantidad"]);
        
        return array(
            "pago"  => $Remuneradas->getPago(),
            "fecha_inicio" => $Remuneradas->getFechaInicio(),
            "fecha_fin" => $Remuneradas->getFechaFin(),
            "cantidad" => $Remuneradas->getCantidad()
        );

    }
    public function setNoRemuneradas($datos)
    {
        $Remuneradas = new Remuneradas();
        $Remuneradas->setFechaInicio($datos["fecha_inicio"]);
        $Remuneradas->setFechaFin($datos["fecha_fin"]);
        $Remuneradas->setCantidad($datos["cantidad"]);
        
        return array(
            "fecha_inicio" => $Remuneradas->getFechaInicio(),
            "fecha_fin" => $Remuneradas->getFechaFin(),
            "cantidad" => $Remuneradas->getCantidad()
        );

    }
    function setLicencias($datos)
    {
        /*$Licencias = new Licencias();
        $Licencias->setMaternidadPaternidad($datos["fecha_inicio"]);
        $Licencias->setRemunerada($datos["fecha_fin"]);
        $Licencias->setNoRemunerada($datos["cantidad"]);*/
        
        return array(
            "mp" => $Licencias->setMaternidadPaternidad($datos["fecha_inicio"]),
            "r" => $Licencias->setRemunerada($datos["fecha_fin"]),
            "nr" => $Licencias->setNoRemunerada($datos["cantidad"])
        );
    }
    function setBonificaciones($datos)
    {
        //$Bonificaciones = new Bonificaciones();
        //$Bonificaciones->setSalariales($datos["salariales"]);
        //$Bonificaciones->setNoSalariales($datos["no_salariales"]);
        
        return array(
            "s" => $datos["salariales"],
            "ns" => $datos["no_salariales"]
        );
    }
    function setAuxilios($datos)
    {
        /*$Auxilios = new Auxilios();
        $Auxilios->setSalariales($datos["salariales"]);
        $Auxilios->setNoSalariales($datos["no_salariales"]);*/
        
        return array(
            "s" => $datos["salariales"],
            "ns" => $datos["no_salariales"]
        );
    }
    public function setHuelgasLegales($datos)
    {
        /*$Remuneradas = new Remuneradas();
        $Remuneradas->setFechaInicio($datos["fecha_inicio"]);
        $Remuneradas->setFechaFin($datos["fecha_fin"]);
        $Remuneradas->setCantidad($datos["cantidad"]);*/
        
        return array(
            "fecha_inicio" => $datos["fecha_inicio"],
            "fecha_fin" => $datos["fecha_fin"],
            "cantidad" => $datos["cantidad"]
        );

    }
    function setEpctv($datos)
    {
        /*$Auxilios = new Auxilios();
        $Auxilios->setSalariales($datos["salariales"]);
        $Auxilios->setNoSalariales($datos["no_salariales"]);*/
        
        return array(
            "s" => $datos["s"],
            "ns" => $datos["ns"],
            "alimentacion_s" => $datos["alimentacion_s"],
            "alimentacion_ns" => $datos["alimentacion_ns"]
        );
    }
    function setOtrosConceptos($datos)
    {
        /*$OtroConceptos = new OtroConceptos();
        $OtroConceptos->setDescripcion($datos["descripcion"]);
        $OtroConceptos->setSalariales($datos["salariales"]);
        $OtroConceptos->setNoSalariales($datos["no_salariales"]);*/
        $arrayOtrosConceptos = [];
        foreach($datos as $key=>$value){
            $array = array(
                "descripcion " => $values["descripcion"],
                "s" => $values["salariales"],
                "ns" => $values["no_salariales"],
            );
            array($arrayOtrosConceptos,$array);
        }
        return $arrayOtrosConceptos;
    }
    public function setComisiones($datos){
        $array = [];
        forEach($datos as $key=>$value){
            array_push($array, $value);
        }
        return $array;
    }
    public function setPagosTerceros($datos){
        $array = [];
        forEach($datos as $key=>$value){
            array_push($array, $value);
        }
        return $array;
    }
    public function setAnticipos($datos){
        $array = [];
        forEach($datos as $key=>$value){
            array_push($array, $value);
        }
        return $array;
    }
}
