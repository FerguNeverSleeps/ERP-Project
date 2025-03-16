var ChatIndex = function () {



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

        muestraMensaje:function(fecha,nombre,imagen,texto,tipo_mensaje){
                var cont = $('#chats');    
                var list = $('.chats', cont);
                //var time = new Date();
                //var time_str = time.toString('MMM dd, yyyy hh:mm');
                var tpl = '';
                tpl += '<li class="'+tipo_mensaje+'">';
                tpl += '<img class="avatar" alt="" src="../../../includes/assets/img/avatar1.jpg"/>';
                tpl += '<div class="message">';
                tpl += '<span class="arrow"></span>';
                tpl += '<a href="#" class="name">'+nombre+'</a>&nbsp;';
                tpl += '<span class="datetime">el ' + fecha + '</span>';
                tpl += '<span class="body">';
                tpl += texto;
                tpl += '</span>';
                tpl += '</div>';
                tpl += '</li>';

                var msg = list.append(tpl);
                
                $('.scroller', cont).slimScroll({
                    scrollTo: list.height()
                });
        },

        cargarMensajes:function(usuario_destino){
            var cont = $('#chats');    
            var list = $('.chats', cont);
            //list.val("");
            $(".chats").empty();
            $.ajax({
              dataType: "json",
              async:true,
              url: "../../../includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/ChatUsuario/Get_List',
                    output:'json',
                    usuario_destino:usuario_destino,
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                //console.log(data)
                for(i=0;i<data.matches.length;i++){
                    ChatIndex.muestraMensaje(data.matches[i].fecha_creacion,data.matches[i].nombre,'',data.matches[i].mensaje,data.matches[i].tipo_mensaje);
                }      
              }
            });
        },

        guardarEvento:function(){
            var cont = $('#chats');
            var list = $('.chats', cont);
            var form = $('.chat-form', cont);
            var input = $('input', form);
            var btn = $('.btn', form);                
            var text = input.val();

            if (text.length == 0) {
                return;
            }
            var usuario_destino = $("#usuario-chat").val();
            $.ajax({
              dataType: "json",
              async:true,
              url: "../../../includes/clases/controladores/ControladorApp.php",
              data: {
                    accion:'Accion/ChatUsuario/Save',
                    operacion:'add',
                    mensaje:text,
                    usuario_destino:usuario_destino,
                    output:'json',
                    db_connect:DB_CONNECT
              },
              success:  function(data){
                    if(data.mensaje = "OK"){
                        input.val("");
                        ChatIndex.cargarMensajes(usuario_destino);
                    }
                    else
                        alert("Ha ocurrido un error");
              }
            });
            
        },

        initChat: function () {

            var cont = $('#chats');
            var list = $('.chats', cont);
            var form = $('.chat-form', cont);
            var input = $('input', form);
            var btn = $('.btn', form);
            var user_sel = $("#usuario-chat");

            var handleClick = function (e) {

                e.preventDefault();
                ChatIndex.guardarEvento();
            }

            var handleSelect = function (e) {

                e.preventDefault();
                var usuario_destino = $("#usuario-chat").val();
                $("#titulo-chat").val("Chats con "+$("#usuario-chat option:selected").html());
                ChatIndex.cargarMensajes(usuario_destino);
            }
            /*
            $('.scroller', cont).slimScroll({
                scrollTo: list.height()
            });
            */

            $('body').on('click', '.message .name', function(e){
                e.preventDefault(); // prevent click event

                var name = $(this).text(); // get clicked user's full name
                input.val('@' +  name + ':'); // set it into the input field
                App.scrollTo(input); // scroll to input if needed
            });

            btn.click(handleClick);

            $("#usuario-chat").change(handleSelect);

            input.keypress(function (e) {
                if (e.which == 13) {
                    handleClick();
                    return false; //<---- Add this line
                }
            });

            ChatIndex.cargarMensajes("");
        }
    };

}();