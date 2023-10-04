<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Datos extends Model
{
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'table_name';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['d_codigo',
'd_asenta',
'd_tipo_asenta',
'D_mnpio',
'd_estado',
'd_ciudad',
'd_CP',
'c_estado',
'c_oficina',
'c_CP',
'c_tipo_asenta',
'c_mnpio',
'id_asenta_cpcons',
'd_zona',
'c_cve_ciudad'];

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
