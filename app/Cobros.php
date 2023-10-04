<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cobros extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cobros';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'monto', 'estado', 'fecha_pago',
        'prox_pago', 'establecimiento_id','observacion','created_at','updated_at','usuario_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Relación de repartidor con usuario(datos personales):
    public function usuario()
    {
        // 1 repartidor pertenece a un usuario
        return $this->belongsTo('App\User', 'usuario_id');
    }
    public function registro()
    {
        // 1 usuario puede tener(ser) un registro
        return $this->hasOne('App\Registro', 'usuario_id', 'usuario_id');
    }
    public function establecimiento()
    {
        // 1 usuario puede tener(ser) un registro
        return $this->hasOne('App\Establecimiento', 'usuario_id', 'usuario_id');
    }
    
    // Relación de repartidor con pedidos:
    public function pedidos()
    {
        // 1 repartidor puede estar en varios pedidos
        return $this->hasMany('App\Pedido', 'repartidor_id');
    }
}
