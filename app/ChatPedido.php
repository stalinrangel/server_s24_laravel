<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatPedido extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chats_pedidos';

    // Eloquent asume que cada tabla tiene una clave primaria con una columna llamada id.
    // Si éste no fuera el caso entonces hay que indicar cuál es nuestra clave primaria en la tabla:
    //protected $primaryKey = 'id';

    //public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['repartidor_id', 'usuario_id', 'pedido_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Relación de chats_pedidos con usuarios (repartidor):
	public function repartidor()
	{
		// 1 msg pertenece a un usuario (repartidor)
		return $this->belongsTo('App\User', 'repartidor_id');
	}
    public function pedido()
	{
		// 1 msg pertenece a un usuario (repartidor)
		return $this->belongsTo('App\Pedido', 'pedido_id');
	}

	// Relación de chats_pedidos con usuarios (cliente):
	public function usuario()
	{
		// 1 msg pertenece a un usuario (cliente)
		return $this->belongsTo('App\User', 'usuario_id');
	}

	// Relación de chats_pedidos con msgs_chats_repartidores:
    public function mensajes()
    {
        // 1 chats_pedidos puede tener varios mensajes
        return $this->hasMany('App\MsgChatPedido', 'chat_id');
    }
}
