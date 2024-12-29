<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Group;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\MessageAtachment;
use App\Models\Conversation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\SocketMessage;

class MessageController extends Controller
{
    public function byUser(User $user)
    {
        $messages = Message::where('sender_id', auth()->id())
            ->where('reciver_id', $user->id)
            ->orWhere('sender_id', $user->id)
            ->where('reciver_id', auth()->id())
            ->latest()
            ->paginate(10)
            ;
            return inertia('Home', [
                'selectedConversation' => $user->toConversationArray(),
                'messages' => MessageResource::collection($messages),
            ]);
    }

    public function byGroup(Group $group)
    {
        $messages = Message::where('group_id', $group->id)
            ->latest()
            ->paginate(10)
            ;
            return inertia('Home', [
                'selectedConversation' => $group->toConversationArray(),
                'messages' => MessageResource::collection($messages),
            ]);
    }
    
    public function loadOlder(Message $message)
    {
        if($message->group_id){
            $messages = Message::where('created_at', '<', $message->created_at)
                ->where('group_id', $message->group_id)
                ->latest()
                ->paginate(10)
                ;
                
        }else{
            $messages = Message::where('created_at', '<', $message->created_at)
                ->where(function($query) use ($message){
                    $query->where('sender_id', $message->sender_id)
                        ->where('reciver_id', $message->reciver_id)
                        ;
                })
                ->orWhere(function($query) use ($message){
                    $query->where('sender_id', $message->reciver_id)
                        ->where('reciver_id', $message->sender_id)
                        ;
                })
                ->latest()
                ->paginate(10)
                ;
        }
        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $data['sender_id'] = auth()->id();
        
        // Debug the incoming data
        \Log::info('Message Data:', $data);
        
        // Make sure either reciver_id or group_id is set
        if (!isset($data['reciver_id']) && !isset($data['group_id'])) {
            return response()->json(['error' => 'Either receiver or group must be specified'], 422);
        }

        // Create the message
        $message = Message::create([
            'message' => $data['message'],
            'sender_id' => $data['sender_id'],
            'reciver_id' => $data['reciver_id'] ?? null,
            'group_id' => $data['group_id'] ?? null,
        ]);

        // Handle file attachments
        $files = $data['attachments'] ?? [];
        $attachments = [];
        if ($files) {
            foreach ($files as $file) {
                $directory = 'attachments/' . Str::random(32);
                Storage::makeDirectory($directory);

                $model = [
                    'message_id' => $message->id,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $file->store($directory, 'public'),
                ];
                $attachment = MessageAttachment::create($model);
                $attachments[] = $attachment;
            }
            $message->attachments = $attachments;
        }

        // Update conversation for user messages
        if (isset($data['reciver_id'])) {
            $conversation = Conversation::where(function($query) use ($data) {
                $query->where('user_id1', auth()->id())
                      ->where('user_id2', $data['reciver_id']);
            })->orWhere(function($query) use ($data) {
                $query->where('user_id1', $data['reciver_id'])
                      ->where('user_id2', auth()->id());
            })->first();

            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_id1' => auth()->id(),
                    'user_id2' => $data['reciver_id'],
                ]);
            }

            Conversation::updateConversationWithMessage($conversation, $message);
        }

        // Update group for group messages
        if (isset($data['group_id'])) {
            $group = Group::findOrFail($data['group_id']);
            $group->updateGroupWithMessage($message);
        }

        // Dispatch the socket event
        SocketMessage::dispatch($message);

        return new MessageResource($message);
    }
    public function destroy(Message $message)
    {
        if($message->sender_id != auth()->id()){
            abort(403);
        }
        $message->delete();
        return response()->noContent();
    }
}
