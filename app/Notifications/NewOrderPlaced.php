<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewOrderPlaced extends Notification
{
    public function __construct(public \App\Models\Order $order) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => 'Pedido Nuevo',
            'message' => "Pedido #{$this->order->id} de {$this->order->user->name}",
            'url'     => route('admin.index'), // o route('admin.orders.show', $this->order)
            'meta'    => ['order_id' => $this->order->id],
        ];
    }
}
