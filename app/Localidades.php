<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localidades extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'localidades';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','clave','nombre', 'codigo_postal', 'latitud','longitud', 'lat','lng', 'altitud','activo','municipio_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Relación de blog con msgBlog:
    public function msgs()
    {
        // 1 blog puede tener varios msgs
        return $this->hasMany('App\MsgBlog', 'blog_id');
    }

}
