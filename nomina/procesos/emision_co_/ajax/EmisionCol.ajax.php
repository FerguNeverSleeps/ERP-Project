<?php

Class EmisionColAjax
{
    public function listarNominasEmisionCol(){
        

		$nominas = EmisionColControlador::listarNominasEmision();

		if(count($nominas)== 0){

 			$datosJson = '{"data": []}';

			echo $datosJson;

			return;

 		}

 		$datosJson = '{

	 	"data": [ ';

	 	foreach ($nominas as $key => $value) {
 
			/*=============================================
			ACCIONES
			=============================================*/

			$acciones = "<div class='btn-group'><button class='btn btn-warning btn-sm editarAlmacen' data-toggle='modal'  data-target='#modalEditarAlmacen' id='".$value["id"]."' nombre='".$value["nombre"]."'   ><i class='fas fa-edit text-white'></i></button><button class='btn btn-danger btn-sm eliminarAlmacen' id='".$value["id"]."'><i class='far fa-trash-alt'></i></button></div>";	
 
            if($value['estado'] == 1){
				$estado = "<span class='right badge badge-success' >ACTIVO</span>";
			}
			else{
				$btnEstado = "";
				$estado = "<span class='right badge badge-danger' >INACTIVO</span>";
			}
			$datosJson.= '[
							
						"'.($key+1).'",
						"'.$value["nombre"].'",
						"'.$estado.'",
						"'.$acciones.'"
						
				],';

		}

		$datosJson = substr($datosJson, 0, -1);

		$datosJson.=  ']

		}';

		echo $datosJson;exit;

	}
    
}