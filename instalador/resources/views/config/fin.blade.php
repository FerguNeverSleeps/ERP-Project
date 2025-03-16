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
	  				<div class="pull-left"><h3><strong>Configuracion Parte V</strong></h3></div>
	  			</div>
  			</div>
  		</div>
      <div class="panel-body">
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			    <div class="panel panel-default">
				    <div class="panel-heading" role="tab" id="heading1">
				      <h4 class="panel-title">
				        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
				          <strong>Intalacion culminada</strong>
				        </a>
				      </h4>
				    </div>
				    <div id="collapse1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading1">
				      <div class="panel-body">
				        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quisquam id fugit, aperiam voluptate inventore pariatur voluptas, cum, excepturi consequuntur doloremque nostrum veniam nulla. Omnis, ex dicta numquam obcaecati quia quisquam?</p><hr>
				        <a href="<?php echo $url; ?>"><input type="buttom" class="btn btn-primary" value="Iniciar aplicacion"></a>
				      </div>
				    </div>
			    </div>
			</div>
      </div>
    </div>
  </div>
  <div class="hidden-xs col-sm-2 col-md-2 col-lg-2"></div>
</div>
@stop