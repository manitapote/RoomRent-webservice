<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Details of post
     * @var Post
     */
    public $data;
    
    /**
     * Array of device tokens
     * @var Array
     */
    public $tokens;

    /**
     * Create a new event instance.
     * @return void
     */
    public function __construct($tokens, $data)
    {
        $this->tokens  = $tokens;
        $this->data    = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
