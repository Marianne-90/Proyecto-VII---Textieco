<?php

namespace App\Observers;

use App\Models\Order;

use App\Models\User; // o Admin
use App\Notifications\NewOrderPlaced;

class OrderObserver
{
    public function created(Order $order): void
    {
        $admins = User::where('utype', 'ADM')->get(); // o Admin::all()
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderPlaced($order));
        }
    }
}
