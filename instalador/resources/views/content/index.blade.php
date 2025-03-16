@extends('layouts.principal')

@section('content')
	@include('alerts.errors')
<div class="row">
	<div class="hidden-xs col-sm-2 col-md-4 col-lg-4"></div>
	<div class="col-xs-12 col-sm-8 col-md-4 col-lg-4">
		<div class="panel panel-default">
			<div class="panel-heading">Login</div>
			<div class="panel-body">
			    {!!Form::open(['route'=>'log.store','method'=>'POST'])!!}
			        @include('usuario.forms.log')
			        {!!Form::submit('Conectar',['class'=>'btn btn-primary'])!!}
	            	{!!link_to_route('usuario.create', $title = 'Nuevo Usuario', $parameters = '', $atributes = ['class'=>'btn btn-primary'])!!}
			    {!!Form::close()!!}
			</div>
		</div>
	</div>
	<div class="hidden-xs col-sm-2 col-md-4 col-lg-4"></div>
</div>
@stop