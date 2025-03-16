<?php

namespace Selectra_planilla;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
	protected $table = 'paises';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'iso', 'nombre',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
}
