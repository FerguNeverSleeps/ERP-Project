<?php

class Util{
	
	//Convierte  cuenta en formato ####### a #.###.### segun formato definido en contabilidad
	public function setFormatoCuentaContable($request,$conf){ 
		try{
			$cuentaAux = "";
			$nivel = 0;
			$salida = "";
			//print_r($conf);
			for ($i=1; $i <= strlen($request['cuenta']); $i++) { 
				//echo $i." - ".$conf['Niv1']." - ".$conf['Sepacta']."****\n";
				if($i == ($nivel+$conf[0]['Niv1']) && $conf[0]['Niv1'] != 0){					
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv1']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv1'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv2']) && $conf[0]['Niv2'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv2']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv2'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv3']) && $conf[0]['Niv3'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv3']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv3'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv4']) && $conf[0]['Niv4'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv4']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv4'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv5']) && $conf[0]['Niv5'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv5']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv5'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv6']) && $conf[0]['Niv6'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv6']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv6'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv7']) && $conf[0]['Niv7'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv7']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv7'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv8']) && $conf[0]['Niv8'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv8']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv8'] = 0;
				}
				elseif($i == ($nivel+$conf[0]['Niv9']) && $conf[0]['Niv9'] != 0){
					$cuentaAux .= substr($request['cuenta'], $nivel,$conf[0]['Niv9']).$conf[0]['Sepacta'];
					$nivel = $i;
					$conf[0]['Niv19'] = 0;
				}
			}

			return  Array('cuenta_vieja' => $request['cuenta'],'cuenta_nueva' => $cuentaAux,"salida" => $salida);
		}
		catch(Exception $ex){
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
}
?> 