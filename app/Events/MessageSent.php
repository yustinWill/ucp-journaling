<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets; // Public Channel
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Broadcast via public channel
    public function broadcastOn()
    {
        return new Channel('public-messages');
    }

    /** With broadcasting off (BROADCAST_CONNECTION=null) nothing is queued, so no job waits for a worker. */
    public function broadcastWhen(): bool
    {
        // env() turns BROADCAST_CONNECTION=null into a real null, so both spellings mean off.
        return ! in_array(config('broadcasting.default'), [null, 'null'], true);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
