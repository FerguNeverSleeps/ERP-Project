
function formatTotal(number, showDecimals, latFormat){
		var amount = "";
		var decimalPos = 0;
		var entera = "";
		var decimalSeparator = "";
		var thousandSeparator = "";
				
		if(latFormat == true){
			decimalSeparator = ",";
			thousandSeparator = ".";
		}else{
			decimalSeparator = ".";
			thousandSeparator = ",";
		}
		number = Math.round(number*100)/100;
		amount = number.toString();				
		decimalPos = amount.indexOf(".");				
		if (decimalPos != -1){		
			entera = amount.substring(0,decimalPos);
			decimal = amount.substring(decimalPos+1, amount.length);						
		}else{
			entera = amount;
		}		
		var preEntera, rEntera = "";
		if ((entera.length % 3) > 0){
			if((entera.length % 3) == 2){
				var k=0;
				var nEntera = "";
				//punto en el segundo y despues de 3 en 3
				preEntera = entera.substring(0,2);
				rEntera = entera.substring(2,entera.length);
				nEntera = preEntera+thousandSeparator;
				if (entera.length > 3){
      				for(i=0;i<=rEntera.length - 1;i++){
      					if (k==3){																
      						nEntera += thousandSeparator;
      						nEntera += rEntera.charAt(i);
      						k=1;
      					}else{																
      						nEntera += rEntera.charAt(i);
      						k += 1;
      					}
      						
      				}
				}else{
					nEntera = preEntera;
				}				
			}else{
				var l=0;
				var nEntera = "";
				//punto en el segundo y despues de 3 en 3
				preEntera = entera.substring(0,1);
				rEntera = entera.substring(1,entera.length);
				nEntera = preEntera+thousandSeparator;
				if (entera.length > 3){
      				for(i=0;i<=rEntera.length - 1;i++){
      					if (l==3){																
      						nEntera += thousandSeparator;
      						nEntera += rEntera.charAt(i);
      						l=1;
      					}else{																
      						nEntera += rEntera.charAt(i);
      						l += 1;
      					}
      						
      				}				
				}else{
					nEntera = entera;
				}
			}
		}else{									
			var c = 0;
			var nEntera = "";			
			if (entera.length > 3){			
      			for(i=0;i<=entera.length - 1;i++){				
      				if (c==3){										
      					nEntera += thousandSeparator;
      					nEntera += entera.charAt(i);
      					c=1;
      				}else{										
      					nEntera += entera.charAt(i);
      					c += 1;
      				}
      					
      			}
			}else{
				nEntera = entera;
			}		
		
		}		
      	if (showDecimals == true){          	
          	if (decimalPos != -1){				 			
      			return(nEntera+decimalSeparator+decimal);
          	}else{
          		return(nEntera+decimalSeparator+"00");
            }
      	}else{
          	
      		return(nEntera);
      	}
}

function rellenarConCeros(numero, digitos) {
    return Array(Math.max(digitos - String(numero).length + 1, 0)).join(0) + numero;
}

function redondearDecimal(numero){
	var entero = parseInt(numero);
	var decimal = numero % 1;
	//decimal = decimal.toFixed(2);
	if(decimal == 0.0){
		return entero;
	}
	else if(decimal <= 0.50){
		return entero +"."+5; 
	}
	return Math.ceil(numero);

}