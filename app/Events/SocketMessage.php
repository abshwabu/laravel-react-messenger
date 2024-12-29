<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Http\Resources\MessageResource;

class SocketMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Message $meassage)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $m = $this->meassage;
        $channel =[];
        if($m->group_id){
            $channel[] = new PrivateChannel("message.group.{$m->group_id}");
        }else{
            $channel[] = new PrivateChannel("message.user.".collect([$m->sender_id, $m->reciver_id])->sort()->implode('-'));
        }
        return $channel;
    }

    public function broadcastWith(): array
    {
        return [
            'message' => new MessageResource($this->meassage),
        ];
    }
}
