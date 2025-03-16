<div class="form-group">
    @include('config.forms.dataHidden')
    <div class="row">
        <div class="col-lg-12">
        <input type="hidden" name="etapa" value="4">
        @foreach($modulos as $modulo)
            <div class="col-sm-4 col-md-4 col-lg-4">
                <div class="pull-right">
                    <?php echo utf8_encode($modulo['nom_menu']);?>
                    <input type="checkbox" name="<?php echo $modulo['cod_modulo']; ?>" value="<?php echo $modulo['cod_modulo']; ?>">
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>