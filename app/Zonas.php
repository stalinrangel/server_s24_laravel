<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zonas extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'zonas';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','coordenadas', 'nombre','costo','ciudad_id','pais_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
   // protected $hidden = ['id'];

    public function ciudad()
    {
        // 1 subcat pertenece a una categoria
        return $this->belongsTo('App\Ciudad', 'ciudad_id');
    }
    public function pais()
    {
        // 1 subcat pertenece a una categoria
        return $this->belongsTo('App\Pais', 'pais_id');
    }

      // Relación de ciudad con productos:
    public function productos(){
        // 1 ciudad puede tener varios productos
        return $this->belongsToMany('\App\Producto','zona_productos','zona_id','producto_id');
    }
}
