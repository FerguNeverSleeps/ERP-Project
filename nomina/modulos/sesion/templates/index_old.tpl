<!DOCTYPE html>
<html>
    <head>
        <!-- <title>.: M&oacute;dulo de Facturaci&oacute;n e Inventario :.</title> -->
        <title>{$titulo_pagina}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!--Aqui estaba una inclusion del header.tpl, {include file="header.tpl"}-->
        <link type="text/css" rel="stylesheet" href="../../../includes/css/login.otro.css" />
        <link type="text/css" rel="stylesheet" href="../../../includes/css/estilos.css" />
        <link rel="shortcut icon" href="../../../includes/imagenes/selectra.ico" />
        {literal}
            <style type="text/css">
                .oculto { display: none; }
                .visible { display: inline; }
            </style>
        {/literal}
        <script type="text/javascript" src="../../../includes/js/jquery-1.11.0.min.js"></script>
    </head>
    <body>
        <div id="login">
            <div id="logo">
                <img src="../../../includes/imagenes/logologin.png"/>
            </div>
            <div id="cont">
                <form method="post">
                    <ul>
                        <li></li>
                        <li>
                            <label>Inicia sesi&oacute;n</label>
                        </li>
                            <!-- <label for="username"><b>Nombre de usuario</b></label> -->
                            <input type="text" name="txtUsuario" id="username" placeholder="Nombre de usuario" class="form-text" />
                            {literal}
                                <script type="text/javascript">//<![CDATA[
                                        document.getElementById("username").focus();
                                //]]>
                                </script>
                            {/literal}
                        </li>
                        <li>
                            <br/>
                            <!-- <label for="password"><b>Contrase&ntilde;a</b></label> -->
                            <input type="password" name="txtContrasena" id="password" placeholder="Contrase&ntilde;a" class="form-text" />
                        </li>
                        <li>
                            <label for="empresaSeleccionada">Seleccione Empresa</label>
                            <select name="empresaSeleccionada" id="empresaSeleccionada">
                                {foreach from=$empresas key=i item=campo}
                                    <option value="{$campo.bd}">{$campo.nombre}</option>
                                {/foreach}                                
                            <select/>
                        </li>
                        <li>
                            <input type="submit" name="submit" id="submit" value="Ingresar" />
                        </li>
                        <!--li>
                            <a style="color: blue" href="../../../sistemas.php" ><img src="../../../includes/imagenes/back.gif"/>&nbsp;Ir al Men&uacute; de Sistemas</a>
                        </li-->
                    </ul>
                    <!--div>
                        <input type="submit" name="submit" id="submit" value="Ingresar" />
                    </div-->
                </form>
            </div>
            <div style="text-align: center;">
                <!--<a style="color: blue;" href="../../../sistemas.php"><img src="../../../includes/imagenes/back.gif"/>&nbsp;Ir al Men&uacute; de Sistemas</a>-->
            </div>
            <div style="text-align: center">
                <span id="mensaje_acceso" class="oculto" style="color: red; white-space: nowrap;">Acceso denegado: Nombre de usuario o contrase&ntilde;a incorrecto.</span>
                {if $acceso eq 0}
                    <br/>
                    <script type="text/javascript">//<![CDATA[
                        document.getElementById("mensaje_acceso").className = "visible";
                    //]]>
                    </script>
                {/if}
            </div>
        </div>
        {literal}
        <script type="text/javascript">
            $(document).ready(function() {
                 //$('.slide-center').click(function(){
                    
                    
                //});           
                 //animate image position to center
                    //our image is 600px and frame is 900px
                    //900-600=300 and 300/2=150
                    //so we set image left to 150px
                //$('#login').animate({
                //    left: 150
                //}, 500);
                /*$('.slide-center').click(function(){
                    
                    //animate image position to center
                    //our image is 600px and frame is 900px
                    //900-600=300 and 300/2=150
                    //so we set image left to 150px
                    $('#login').animate({
                        left: 550
                    }, 500);
                });*/
            });
    </script>
    {/literal}
    </body>
</html>