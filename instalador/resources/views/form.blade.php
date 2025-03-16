@extends('layouts.principal')

@section('content')
    <div class="content">
        <label for=""><h1>Registro de empresas</h1></label>
        <form action="recibir" method="post">
            <label for=""><strong>Nombre:</strong></label>
            <input type="text" name="nombre" id="nombre">
            <label for=""><strong>RIF:</strong></label>
            <input type="text" name="rif" id="rif">
            <input type="submit" value="Enviar">
        </form>
    </div>
@stop