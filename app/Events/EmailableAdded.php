<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailableAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $email;
    public $data;
    public $type;

    /**
     * Create a new event instance.
     *
     * @param $email
     * @param $type
     * @param $data
     * @return void
     */
    public function __construct(string $type, string $email, Model $data)
    {
        $this->type = $type;
        $this->email = $email;
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
