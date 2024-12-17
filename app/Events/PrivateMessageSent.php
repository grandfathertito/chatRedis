<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $sender_id;
    public $recipient_id;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $sender_id, $recipient_id)
    {
        //
        $this->message = $message;
        $this->sender_id = $sender_id;
        $this->recipient_id = $recipient_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("private-chat.{$this->recipient_id}"),
        ];
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}
