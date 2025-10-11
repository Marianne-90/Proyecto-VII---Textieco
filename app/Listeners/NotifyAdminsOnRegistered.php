<?php

// app/Listeners/NotifyAdminsOnRegistered.php
namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Notifications\NewUserRegistered;
use App\Models\User; // o App\Models\Admin si tienes modelo separado

class NotifyAdminsOnRegistered
{
    public function handle(Registered $event): void
    {
        // Selecciona a tus admins
        $admins = User::where('utype', 'ADM')->get(); // ajusta si usas Admin::all()

        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegistered($event->user));
        }
    }
}

