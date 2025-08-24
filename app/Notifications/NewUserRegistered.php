<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification
{
    public function __construct(public \App\Models\User $newUser) {}

    public function via($notifiable)
    {
        return ['database']; // solo en DB
    }

    public function toDatabase($notifiable)
    {
        return [
            'title'   => 'New User Registered',
            'message' => "User {$this->newUser->name} ({$this->newUser->email}) has registered.",
            'url'     => route('admin.index'), // cámbialo si tienes una vista de detalle
            'meta'    => ['user_id' => $this->newUser->id],
        ];
    }
}
