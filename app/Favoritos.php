<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favoritos extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'favoritos';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['establecimiento_id', 'usuario_id','productos_id','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    //protected $hidden = ['created_at','updated_at'];

    // Relación de establecimiento con productos:
    public function productos()
    {
        // 1 establecimiento puede tener varios productos
        return $this->hasMany('App\Producto', 'id','establecimiento_id');
    }


    // Relación de establecimiento con usuario(datos de acceso):
    public function usuario()
    {
        // 1 establecimiento pertenece a un usuario
        return $this->belongsTo('App\User', 'usuario_id');
    }

}
