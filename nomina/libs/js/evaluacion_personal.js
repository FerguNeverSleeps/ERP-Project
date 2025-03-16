function get_browser(){
    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || []; 
    if(/trident/i.test(M[1])){
        tem=/\brv[ :]+(\d+)/g.exec(ua) || []; 
        return 'IE '+(tem[1]||'');
        }   
    if(M[1]==='Chrome'){
        tem=ua.match(/\bOPR\/(\d+)/)
        if(tem!=null)   {return 'Opera '+tem[1];}
        }   
    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
    return M[0];
    }

function get_browser_version(){
    var ua=navigator.userAgent,tem,M=ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];                                                                                                                         
    if(/trident/i.test(M[1])){
        tem=/\brv[ :]+(\d+)/g.exec(ua) || [];
        return 'IE '+(tem[1]||'');
        }
    if(M[1]==='Chrome'){
        tem=ua.match(/\bOPR\/(\d+)/)
        if(tem!=null)   {return 'Opera '+tem[1];}
        }   
    M=M[2]? [M[1], M[2]]: [navigator.appName, navigator.appVersion, '-?'];
    if((tem=ua.match(/version\/(\d+)/i))!=null) {M.splice(1,1,tem[1]);}
    return M[1];
    }


/**
 * OTHERS:
 * Para cada input con un open asociado, vincula dos eventos:
 * - Cuando el unput cambie, lleva el valor a un atributo backup o lo trae de vuelta.
 * - En el focus del open, marca como seleccionado el input
 */
function activaOthers(){
	$('input[data-openFieldName]').each(function(){
		var $input = $(this);
		var $open = $('[name="'+$input.attr('data-openFieldName')+'"]');
		// Tenemos que vincular el evento a todos los inputs con el mismo nombre que el input, aunque luego solamente comprobemos si la propiedad checked de $input:
		$('input[name="'+$input.attr('name')+'"]').change(function(){
			if($input.prop('checked')){
				$open.val($open.attr('data-backup')); // Recuperar backup
			}else{
				$open.attr('data-backup',$open.val()).val(''); // Crear backup
			}
		});
		$open.focus(function(){
			if(!$input.prop('checked'))$input.prop('checked',true).change();
		});
	})
}

/**
 * PREGUNTAS TIPO ORDENACION
 */
function activaSorting(){
	$('.q_sortable').each(function(){
		var $list = $(this);
		$list.sortable({placeholder: "q_sortable_placeholder",  update: rearrange, start: function(e, ui){ui.placeholder.height(ui.item.height());}}).disableSelection();
		// Por si tienen que venir ordenadas de alguna forma (pulsamos "atrÃ¡s", valores por defecto...
		var $listItems = $list.find('li').sort(function(a, b) {
			var comp = $(a).find('input').val()-$(b).find('input').val();
			return comp<0?-1:(comp==0?0:1); 
		});
		$list.find('li').remove();
		$list.append($listItems);
	});
}
function rearrange(event){
	$(event.target).children().each(function(index){$(this).find('input').val(index+1)});
}

function oneOptionPerColumn(el, rowNames) {
	var rows = rowNames.split(' ');
	var els = el.form[el.name];
	for(var c = 0; c < els.length;c++){
		if (els[c] == el) {
			for ( var r in rows) {
				if (rows[r] != el.name) {
					el.form[rows[r]][c].checked = false;
				}
			}
			return;
		}
	}
}

function loadjs(filename) {
	var fileref = document.createElement('script');
	fileref.setAttribute("type", "text/javascript");
	fileref.setAttribute("src", filename);
	document.getElementsByTagName("head")[0].appendChild(fileref);
}

function loadcss(filename) {
	var fileref = document.createElement("link");
	fileref.setAttribute("rel", "stylesheet");
	fileref.setAttribute("type", "text/css");
	fileref.setAttribute("href", filename);
	document.getElementsByTagName("head")[0].appendChild(fileref);
}

function enableLimitOptions(){
	$('.question[data-MAX_NUM_ANSWERS]').each(function(){
		var $question = $(this);
		var limit = $question.attr('data-MAX_NUM_ANSWERS');
		// Lo comprobamos al cargar y siempre que cambien:
		checkMaxNumOptions($question,limit);
		$question.find('input[type=checkbox]').change(function(){checkMaxNumOptions($question,limit);});
	});
	
	$('.question[data-MIN_NUM_ANSWERS]').each(function(){
		var $question = $(this);
		var limit = $question.attr('data-MIN_NUM_ANSWERS');
		// TODO
	});
}

function checkMaxNumOptions($question, limit){
	var bol = $question.find("input[type=checkbox]:checked").length >= limit;     
	$question.find("input[type=checkbox]").not(":checked").each(function(){
		var $this = $(this);
		$this.attr("disabled",bol);
		// Hay que deshabilitar el campo others tb:
		var inputOpenName =$this.attr('data-openFieldName'); 
		if(inputOpenName){
			$('input[name="'+inputOpenName+'"]').attr("disabled",bol); 	 
		}
	});
}

window.onload=function () {
	var browser=get_browser();
	var browser_version=get_browser_version();
	if((browser == "MSIE" && browser_version < 9) || (browser == "Firefox" && browser_version < 17)){
		var inputs = document.getElementsByTagName('input');
		for (index = 0; index < inputs.length; index++){
			if(inputs[index] && inputs[index].onchange && inputs[index].onchange != 'undefined'){
				inputs[index].onclick = inputs[index].onchange;
			}
		}
	}
	activaOthers();
	enableLimitOptions();
}