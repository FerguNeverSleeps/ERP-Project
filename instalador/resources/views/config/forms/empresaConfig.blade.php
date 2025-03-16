@include('config.forms.dataHidden')
<div class="form-group">
    {!!Form::label('Nombre Empresa:')!!}
    <input type="hidden" name="etapa" value="3">
    <input type="text" name="nombre" value="<?php if(isset($empresa->nombre)){echo $empresa->nombre; }?>" class="form-control" placeholder="Ingresa nombre de usuario" required>
</div>
<div class="form-group">
    {!!Form::label('Pais:')!!}
    <select name = "pais_id" class = "form-control" required>
        @foreach($paises as $pais)
        <option value="<?php echo $pais['id'];?>"><?php echo utf8_encode($pais['nombre'])."-".utf8_encode($pais['iso']);?></option>
        @endforeach
    </select>
</div>
<div class="form-group" required>
    {!!Form::label('Logo Empresa:')!!}
    <input type="file" class="form-control" name="archivo" >
</div>