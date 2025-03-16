@extends('layouts.principal')

@section('content')
<div class="row">
  <div class="hidden-xs col-sm-2 col-md-2 col-lg-2"></div>
  <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
    <div class="panel panel-primary">
      <div class="panel-heading">
      		<div class="row">
	  			<div class="hidden-xs col-sm-4 col-md-4 col-lg-4">
	  				<img src="../storage/app/gtvl.jpg" alt="..." width="125px" height="60px" class="img-rounded pull-left">
	  			</div>
	  			<div class="hidden-xs col-sm-8 col-md-8 col-lg-8">
	  				<div class="pull-left"><h3><strong>Configuracion Parte IV</strong></h3></div>
	  			</div>
  			</div>
  		</div>
      <div class="panel-body">
        {!!Form::open(['route'=>'config.store','method'=>'POST','files'=>true])!!}
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			  <div class="panel panel-default">
					
			    <div class="panel-heading" role="tab" id="heading1">
			      <h4 class="panel-title">
			        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
			          <strong>Modulos principales</strong>
			        </a>
			      </h4>
			    </div>
					
			    <div id="collapse1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading1">
			      <div class="panel-body">
			        @include('config.forms.modP_Config')
			      </div>
			    </div>

			  </div>
			</div>
          {!!Form::submit('Siguiente',['class'=>'btn btn-primary'])!!}
          {!!Form::reset('Limpiar',['class'=>'btn btn-primary'])!!}
        {!!Form::close()!!}
      </div>
    </div>
  </div>
  <div class="hidden-xs col-sm-2 col-md-2 col-lg-2"></div>
</div>
@stop