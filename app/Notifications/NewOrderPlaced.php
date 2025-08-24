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
            'title'   => 'New Order',
            'message' => "Order #{$this->order->id} from {$this->order->user->name}",
            'url'     => route('admin.index'), // o route('admin.orders.show', $this->order)
            'meta'    => ['order_id' => $this->order->id],
        ];
    }
}
