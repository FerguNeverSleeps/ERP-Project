var DIR = "../../../";
if(typeof DIR_INCLUDES != 'undefined'){
    DIR = DIR_INCLUDES;
}

var CalendarIndex = function () {



    return {

        //main function
        init: function () {
            App.addResponsiveHandler(function () {
                jQuery('.vmaps').each(function () {
                    var map = jQuery(this);
                    map.width(map.parent().width());
                });
            });
        },

        guardarEvento:function(){
            var ini = $("#fecha-inicio-calendar").val();
            var fin = $("#fecha-fin-calendar").val();

            var evento = $("#nombre-evento").val();
            var color = $("#color-evento").val();
            var fecha_inicio = ini.substring(0,10);
            var hora_inicio = ini.substring(13)+":00";
            var fecha_fin = fin.substring(0,10);
            var hora_fin = fin.substring(13)+":00";

            $.ajax({
              dataType: "json",
              async:false,
              url: DIR+"includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/EventoUsuario/Save',
                    operacion:'add',
                    evento:evento,
                    fecha_inicio:fecha_inicio,
                    hora_inicio:hora_inicio,
                    fecha_fin:fecha_fin,
                    hora_fin:hora_fin,
                    color:color,
                    output:'json',
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                    if(data.mensaje = "OK"){
                        $("#fecha-inicio-calendar").val("");
                        $("#fecha-fin-calendar").val("");
                        $("#nombre-evento").val("");
                        CalendarIndex.initCalendar();
                    }
                    else
                        alert("Ha ocurrido un error");
              }
            });
        },

        initCalendar: function () {
            if (!jQuery().fullCalendar) {
                return;
            }

            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();

            var h = {};

            if ($('#calendar').width() <= 400) {
                $('#calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month,agendaWeek,agendaDay'
                };
            } else {
                $('#calendar').removeClass("mobile");
                if (App.isRTL()) {
                    h = {
                        right: 'title',
                        center: '',
                        left: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                } else {
                    h = {
                        left: 'title',
                        center: '',
                        right: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                }               
            }
            /*var responseAjax = $.ajax({
                type: "POST",
                async:false,
                url: '../../../includes/clases/controladores/ControladorApp.php',
                    data: {
                        accion:'Accion/EventoUsuario/Get_List_Month',
                        mes:5
                        output:'json'                        
                    },
                    success: function(data){
                }
            }).responseText;
            //var responseOBJ =$.parseJSON(responseAjax);
            */
            var colores = ["yellow","blue","green","grey","red","purple"]
            var dataEventos = [];
            $.ajax({
              dataType: "json",
              async:false,
              url: DIR+"includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/EventoUsuario/Get_List_Month',
                    tipo_calendario:$('#tipo_calendario').val(),
                    mes:((new Date()).getMonth()+1),
                    output:'json',
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                //console.log(data)
                var lengthEvento = 0;
                var eventoDes = "";
                for(i=0;i<data.matches.length;i++){
                    //console.log(parseInt(data.matches[i].fecha_inicio.substring(0, 4)), parseInt(data.matches[i].fecha_inicio.substring(5, 7)), parseInt(data.matches[i].fecha_inicio.substring(8)),colores[(Math.floor(Math.random() * 5))]);
                    lengthEvento = data.matches[i].evento.length;
                    if(lengthEvento >=15)
                        eventoDes = data.matches[i].evento.substring(0, 14)+"...";
                    else
                        eventoDes = data.matches[i].evento;
                    dataEventos.push({
                        title: eventoDes,
                        allDay: false,
                        start: new Date(parseInt(data.matches[i].fecha_inicio.substring(0, 4)), (parseInt(data.matches[i].fecha_inicio.substring(5, 7))-1), parseInt(data.matches[i].fecha_inicio.substring(8)),parseInt(data.matches[i].hora_inicio.substring(0,2)),parseInt(data.matches[i].hora_inicio.substring(3,5))),
                        end: new Date(parseInt(data.matches[i].fecha_fin.substring(0, 4)), (parseInt(data.matches[i].fecha_fin.substring(5, 7))-1), parseInt(data.matches[i].fecha_fin.substring(8)),parseInt(data.matches[i].hora_inicio.substring(0,2)),parseInt(data.matches[i].hora_inicio.substring(3,5))),
                        backgroundColor: App.getLayoutColorCode(data.matches[i].color)//colores[(Math.floor(Math.random() * 5))]                        
                    });
                }
              }
            });
            
            //console.log("asda",dataEventos)
            $('#calendar').fullCalendar('destroy'); // destroy the calendar
            $('#calendar').fullCalendar({ //re-initialize the calendar
                disableDragging: false,
                header: h,
                editable: true,
                events: dataEventos
                    /*{
                        title: 'All Day Event',                        
                        start: new Date(y, m, 1),
                        backgroundColor: App.getLayoutColorCode('yellow')
                    }, {
                        title: 'Long Event',
                        start: new Date(y, m, d - 5),
                        end: new Date(y, m, d - 2),
                        backgroundColor: App.getLayoutColorCode('green')
                    }, {
                        title: 'Repeating Event',
                        start: new Date(y, m, d - 3, 16, 0),
                        allDay: false,
                        backgroundColor: App.getLayoutColorCode('red')
                    }, {
                        title: 'Repeating Event',
                        start: new Date(y, m, d + 4, 16, 0),
                        allDay: false,
                        backgroundColor: App.getLayoutColorCode('green')
                    }, {
                        title: 'Meeting',
                        start: new Date(y, m, d, 10, 30),
                        allDay: false,
                    }, {
                        title: 'Lunch',
                        start: new Date(y, m, d, 12, 0),
                        end: new Date(y, m, d, 14, 0),
                        backgroundColor: App.getLayoutColorCode('grey'),
                        allDay: false,
                    }, {
                        title: 'Birthday Party',
                        start: new Date(y, m, d + 1, 19, 0),
                        end: new Date(y, m, d + 1, 22, 30),
                        backgroundColor: App.getLayoutColorCode('purple'),
                        allDay: false,
                    }, {
                        title: 'Click for Google',
                        start: new Date(y, m, 28),
                        end: new Date(y, m, 29),
                        backgroundColor: App.getLayoutColorCode('yellow'),
                        url: 'http://google.com/',
                    }*/
            });
        }
    };

}();