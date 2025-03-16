<?php

include_once("zoho_contable_funciones.php");
//Array ( [access_token] => 1000.f97ebe81511ec6b55ee0d68c07c45f9f.8622a396481fe6d1829c493ba67a6d34 [refresh_token] => 1000.9a6479c280051637ebfb09f323aaa907.80d28dc9a81c9e5ef6bc612dd851ec75 [api_domain] => https://www.zohoapis.com [token_type] => Bearer [expires_in] => 3600 )


$retorno=zoho_revoke_token("1000.9a6479c280051637ebfb09f323aaa907.80d28dc9a81c9e5ef6bc612dd851ec75");
print_r($retorno);

?>