<?php


//$client_id="1000.SWGU38TWVBK15D4U8RS1HFZ5GN33RR";
//$client_secret="34f1f1b06536bb5144390afa2ae98cd6aff145d4fc";
//$nomina_electronica_token="1000.79ec6aa09c8bc0e276b91066064602c6.ca970a55bc7d7f090ebd7a39c8d489ad";
//$organization_id="743814609";


//1)CREAR SERVER-BASED APLICATIONS EN https://api-console.nomina_electronica.com/
//2)Darle Acceso a la aplicacion
//https://accounts.nomina_electronica.com/oauth/v2/auth?scope=nomina_electronicaBooks.fullaccess.all&client_id=1000.GP1SJURFLIY5GQZGCX1ZK52T5NZ3GE&state=testing&response_type=code&redirect_uri=http://roja/amaxonia/amaxonia_planilla/nomina/procesos/nomina_electronica/&access_type=offline





function nomina_electronica_journals_create($token,$json){

	$header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token),
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ];

    if(is_array($json)){
    	$json_string=json_encode($json);
    }
    else{
    	$json_string=$json;
    }


    $params=[
        "JSONString"  => $json_string,
    ];

	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://books.nomina_electronica.com/api/v3/journals",
        CURLOPT_POST           => 1,
        CURLOPT_TIMEOUT        => 60*5,
        CURLOPT_POSTFIELDS     => http_build_query($params)
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl);
    //print $response; 
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_get_organization($token){
	$header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token)
    ];


	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://books.nomina_electronica.com/api/v3/organizations",
        //CURLOPT_POST           => 1,
        CURLOPT_TIMEOUT        => 60*5,
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_access_token($access_code, $client_id, $client_secret, $redirect_uri){

	$params=[
        "code"          => $access_code,
        "client_id"     => $client_id,
        "client_secret" => $client_secret,
        "redirect_uri"  => $redirect_uri,
        "grant_type"    => "authorization_code",
        "scope"         => "nomina_electronicaBooks.fullaccess.all",
        "state"         => "testing"
    ];

	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://accounts.nomina_electronica.com/oauth/v2/token",
        CURLOPT_POST           => 1,
        CURLOPT_TIMEOUT        => 60*5,
        CURLOPT_POSTFIELDS     => http_build_query($params)
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    //print $response;
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_revoke_token($refresh_token){	
	$params=[
        "refresh_token" => $refresh_token
    ];

	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://accounts.nomina_electronica.com/oauth/v2/token/revoke",
        CURLOPT_POST           => 1,
        CURLOPT_TIMEOUT        => 60*5,
        CURLOPT_POSTFIELDS     => http_build_query($params)
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    //print $response;
    $response=json_decode($response,true);

    return $response;
}


function nomina_electronica_get_chartofaccounts($token,$organization_id){
	$header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token),
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ];


	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://books.nomina_electronica.com/api/v3/chartofaccounts?organization_id={$organization_id}",
        CURLOPT_TIMEOUT        => 60*5,
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_get_tags($token,$organization_id,$tag_id=NULL){
    $header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token),
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ];

    $url="https://books.nomina_electronica.com/api/v3/settings/tags?organization_id={$organization_id}";
    if($tag_id){
        $url="https://books.nomina_electronica.com/api/v3/settings/tags/{$tag_id}?organization_id={$organization_id}";
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => $url,
        CURLOPT_TIMEOUT        => 60*5,
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
        curl_close($curl); 
        return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_journals_delete($token,$organization_id,$journal_id){
	$header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token),
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ];

	$curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST  => "DELETE",
        CURLOPT_URL            => "https://books.nomina_electronica.com/api/v3/journals/{$journal_id}?organization_id={$organization_id}",
        CURLOPT_TIMEOUT        => 60*5,
    ]);
    $response = curl_exec($curl);
    if(curl_errno($curl)){
      	curl_close($curl); 
    	return ["success"=>false,"message"=>"Curl Error"];
    }
    curl_close($curl); 
    $response=json_decode($response,true);

    return $response;
}

function nomina_electronica_journals_attachment($token,$organization_id,$journal_id, $file, $type="pdf"){

    $header=[
        "Authorization: nomina_electronica-oauthtoken ".trim($token),
        "Content-Type: multipart/form-data; boundary=----WebKitFormBoundarycUUc1jSgTUZmEepF"
    ];

    if($type=="xls"){
        $body=implode("\r\n",[
            '------WebKitFormBoundarycUUc1jSgTUZmEepF',
            'Content-Disposition: form-data; name="attachment"; filename="planilla.xlsx"',
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            '',
            file_get_contents("$file"),
            '------WebKitFormBoundarycUUc1jSgTUZmEepF--',
        ]);        
    }
    else {
        $body=implode("\r\n",[
            '------WebKitFormBoundarycUUc1jSgTUZmEepF',
            'Content-Disposition: form-data; name="attachment"; filename="comprobante.pdf"',
            'Content-Type: application/pdf',
            '',
            file_get_contents("$file"),
            '------WebKitFormBoundarycUUc1jSgTUZmEepF--',
        ]);   
    }


    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_POST           => 1,
        CURLOPT_URL            => "https://books.nomina_electronica.com/api/v3/journals/{$journal_id}/attachment?organization_id={$organization_id}",
        CURLOPT_TIMEOUT        => 60*5,
        CURLOPT_POSTFIELDS     => $body,
    ]);

    $response = curl_exec($curl);
    if(curl_errno($curl)){
        curl_close($curl); 
        return ["success"=>false,"message"=>"Curl Error: ".curl_error($curl)];
    }
    curl_close($curl); 
    $response=json_decode($response,true);
    return $response;
}



?>