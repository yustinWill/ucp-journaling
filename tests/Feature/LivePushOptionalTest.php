<?php

namespace Tests\Feature;

use App\Events\MessageSent;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LivePushOptionalTest extends TestCase
{
    public function test_nothing_is_queued_when_broadcasting_is_off(): void
    {
        Queue::fake();
        foreach ([null, 'null'] as $off) { // .env BROADCAST_CONNECTION=null arrives as a real null
            config(['broadcasting.default' => $off]);
            event(new MessageSent(['title' => 'SO baru']));
        }

        Queue::assertNotPushed(BroadcastEvent::class);
    }

    public function test_the_push_is_queued_when_broadcasting_is_on(): void
    {
        Queue::fake();
        config(['broadcasting.default' => 'log']);

        event(new MessageSent(['title' => 'SO baru']));

        Queue::assertPushed(BroadcastEvent::class);
    }
}
