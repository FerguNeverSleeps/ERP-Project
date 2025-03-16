<!DOCTYPE html>
<html>
    <head>
        <title>Ginteven - Configuracion</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        {!! Html::style('assets/css/bootstrap.css') !!}
        {!! Html::style('assets/css/fileinput.min.css') !!}
        <style>
            .cuerpo {
                text-align: center;
                display: inline-block;
                vertical-align: middle;
                margin-top: 5%;
            }
            .contenido {

                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 cuerpo">
                    @yield('content')  
                </div>    
            </div>
        </div>
        <!-- Scripts -->
        {!! Html::script('assets/js/jquery.min.js') !!}
        {!! Html::script('assets/js/bootstrap.min.js') !!}
        {!! Html::script('assets/js/fileinput.js') !!}
    </body>
</html>
