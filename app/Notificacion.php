<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notificaciones';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'mensaje', 'usuario_id','visto','accion','data','id_operacion','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    //protected $hidden = ['created_at','updated_at'];

   
    public function usuario()
    {
        // 1 pedido pertenece a un usuario
        return $this->belongsTo('App\User', 'usuario_id');
    }

}
