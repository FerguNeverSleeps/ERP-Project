<div class="form-group">
    {!!Form::label('Servidor (host):')!!}
    <input type="hidden" name="etapa" value="1">
    <input type="text" name="host" class="form-control" placeholder="Datos del servidor" required>
</div>
<div class="form-group">
    {!!Form::label('Usuario (host):')!!}
    <input type="text" name="usuario" class="form-control" placeholder="Usuario" required>
</div>
<div class="form-group">
    {!!Form::label('Clave:')!!}
    <input type="password" name="s_password" class="form-control" placeholder="Clave de acceso">
</div>
<hr>
<div class="form-group">
    {!!Form::label('Base de datos:(Cliente)')!!}
    <input type="hidden" name="etapa" value="1">
    <input type="text" name="database1" class="form-control" placeholder="Nombre base de datos cliente" required>
</div>