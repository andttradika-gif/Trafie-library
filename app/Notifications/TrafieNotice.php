<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TrafieNotice extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public ?string $url = null,
        public string $icon = 'bell',
        public ?string $key = null,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url, 'icon' => $this->icon, 'key' => $this->key];
    }
}