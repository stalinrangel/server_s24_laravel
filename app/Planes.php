<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Planes extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'planes';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tipo_plan', 'costo','tipo','descripcion','descuento', 'recomendado','pais_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    public function restricciones()
    {
        return $this->hasMany('App\Planes_restricciones', 'plan_id');
    }

}
