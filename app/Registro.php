<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'registro';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tipo','ruc', 'latitud', 'longitud', 'email', 'contacto_nombre', 'contacto_cedula', 'contacto_cargo', 'operaciones', 'logo', 'cedula', 'sexo', 'nacionalidad', 'direccion', 'direccion_exacta','fecha_nacimiento', 'formacion', 'experiencia', 'anos_experiencia', 'idoneidad', 'disponibilidad', 'idiomas', 'urgencias', 'factura', 'referencias', 'referencias2', 'foto', 'pasaporte', 'idoneidad_file','record_policivo','recibo_servicio', 'estado', 'usuario_id','contrato','observacion','created_at','updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    //protected $hidden = ['created_at','updated_at'];

    // Relación de repartidor con usuario(datos personales):
    public function usuario()
    {
        // 1 repartidor pertenece a un usuario
        return $this->belongsTo('App\User', 'usuario_id');
    }
}
