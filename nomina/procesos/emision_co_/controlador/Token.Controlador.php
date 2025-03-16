<?php

Class TokenControlador
{

    static function enviarNominaIndividual($nomina_individual)
    {
        $token = Token::SolicitarToken();
        $token = json_decode($token,true);
        ParametrosControlador::updateToken($token["access_token"]);
      return Token::enviarNominaIndividual($token["access_token"], $nomina_individual);
    }
    static function consultarNominaIndividual($track_id)
    {
      $parametros = ParametrosControlador::getParametros();
      if( $parametros["bearer_token"] != "")
      {
        $token = Token::SolicitarToken();
        $token = json_decode($token,true);
      }else{
        $token["access_token"] = $parametros["bearer_token"];
      }
      return Token::consultarNominaIndividual($token["access_token"], $track_id);
    }
}
