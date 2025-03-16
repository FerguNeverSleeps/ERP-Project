<?php

namespace Selectra_planilla;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'nomempresa';
    protected $primaryKey='codigo';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre', 'bd_nomina',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
