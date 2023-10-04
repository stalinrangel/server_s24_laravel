<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonas_productos extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zonas_productos';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si �ste no fuera el caso entonces hay que indicar cu�l es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','zona_id','producto_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
   // protected $hidden = ['id'];

}
