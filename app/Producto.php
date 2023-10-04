<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'productos';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre', 'ingles', 'precio', 'descripcion',
        'estado', 'codigo', 'subcategoria_id', 'establecimiento_id','imagen','idoneidad', 'anos_experiencia','habilitado','fotos','zona_id','count_vistas'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['created_at','updated_at'];

    // Relación de producto con zonas:
    public function zonas()
    {
        // 1 producto pertenece a una zonas
        return $this->belongsTo('App\Zonas', 'zona_id');
    }

    // Relación de producto con subcategoria:
    public function subcategoria()
    {
        // 1 producto pertenece a una subcategoria
        return $this->belongsTo('App\Subcategoria', 'subcategoria_id');
    }

    // Relación de producto con establecimiento:
    public function establecimiento()
    {
        // 1 producto pertenece a un establecimiento
        return $this->belongsTo('App\Establecimiento', 'establecimiento_id');
    }

    // Relación de producto con pedidos:
    public function pedidos(){
        // 1 producto puede estar en muchos pedidos
        return $this->belongsToMany('\App\Pedido','pedido_productos','producto_id','pedido_id'); 
    }

    // Relación de producto con sus fotos:
    public function fotos(){
        // 
        return $this->hasMany('\App\Producto_foto','producto_id'); 
    }

    // Relación de producto con ciudades:
    public function zonas2(){
        // 1 producto puede estar en varias ciudades
        return $this->belongsToMany('\App\Zonas','zonas_productos','producto_id','zonas_id');
    }
}
