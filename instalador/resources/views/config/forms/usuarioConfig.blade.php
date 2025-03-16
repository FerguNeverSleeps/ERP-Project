@include('config.forms.dataHidden')
<div class="form-group">
    {!!Form::label('Usuario administrador (Cliente):')!!}
    <input type="hidden" name="etapa" value="2">
    <input type="text" name="usuario_admin" value="<?php if(isset($usuarios->usuario_admin)){echo $usuarios->usuario_admin; }?>" class="form-control" placeholder="Ingresa usuario" required>
</div>
<div class="form-group">
    {!!Form::label('Correo:')!!}
    <input type="email" name="correo" value="<?php if(isset($usuarios->correo)){echo $usuarios->correo; }?>" class="form-control" placeholder="Correo usuario" required>
</div>
<div class="form-group">
    {!!Form::label('Clave administrador:')!!}
    <input type="password" name="a_password" value="<?php if(isset($usuarios->a_password)){echo $usuarios->a_password; }?>" class="form-control" placeholder="Clave de acceso" required>
</div>