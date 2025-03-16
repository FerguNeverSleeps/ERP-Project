<div class="form-group">
    {!!Form::label('Nombre Sistema:')!!}
    <input type="text" name="nombre_sistema" value="<?php if(isset($sistema->nombre_sistema)){echo $sistema->nombre_sistema; }?>" class="form-control" placeholder="Nombre del sistema">
</div>