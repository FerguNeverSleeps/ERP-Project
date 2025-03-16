<?php

namespace Selectra_planilla;

use Illuminate\Database\Eloquent\Model;

class Datosempresa extends Model
{
    protected $table = 'datos_empresa';
    protected $primaryKey = 'cod_datos_empresa';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_empresa','img_izq','nombre_sistema','pais_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
