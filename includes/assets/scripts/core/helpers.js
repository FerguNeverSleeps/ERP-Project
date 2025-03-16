var HelpersCustom = {};

HelpersCustom.convertirNumero = function( valor, tipo){
	var strValue = _.str.trim(valor.toString()),
		value = 0;
	
	if ( _.str.isBlank(strValue) ){
		valor = 0;
	} else {
		strValue = strValue.replace(",",".")
		
		if(tipo=="float"){
			if( _.isNaN(parseFloat(strValue)) ) {
				valor = 0;
			} else {
				value = parseFloat(strValue); 
			}
		}else if(tipo == "integer"){
			if( _.isNaN(parseInt(strValue)) ) {
				valor = 0;
			} else {
				value = parseInt(strValue); 
			}
		}
	}
	if(tipo=="float"){
		return value.toFixed(2);
	}else{
		return value;
	}
}

HelpersCustom.redondear_monto = function(monto){
	return Math.round(monto*Math.pow(10,2))/Math.pow(10,2);
}