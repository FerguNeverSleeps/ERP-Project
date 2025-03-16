<?php
Class Token 
{
    static public function SolicitarToken(){
        $curl = curl_init();
        $data_json = json_encode(array(
            'grant_type' => 'password',
            'client_id' => '3',
            'client_secret' => 'UFkpSFOt0wGCx02bwc5MELXrbWaSyGMkBZKwIyLi',
            'username' => 'nmolina@nmspanama.com',
            'password' => 'emision2021*'
        ));
        
		curl_setopt($curl, CURLOPT_URL, 'https://sso.emision.co/oauth/token');
		curl_setopt(
			$curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
			    'Content-Type: application/json',
			)
		);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$data_json);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    static public function enviarNominaIndividual($token, $nomina_individual){
        
        $curl = curl_init();
        file_put_contents("1nomina.txt",$nomina_individual);
        
		curl_setopt($curl, CURLOPT_URL, 'https://api.payroll.emision.co/api/v1/service/payrolls/test');
		curl_setopt(
			$curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
			    'Content-Type: application/json',
                'Authorization: Bearer '.$token
			)
		);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$nomina_individual);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    static public function consultarNominaIndividual($token, $test_id){
        
        $curl = curl_init();
        $url = 'https://api.payroll.emision.co/api/v1/service/payrolls/'.$test_id;
        
		curl_setopt($curl, CURLOPT_URL,$url);
		curl_setopt(
			$curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
			    'Content-Type: application/json',
                'Authorization: Bearer '.$token
			)
		);

		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}