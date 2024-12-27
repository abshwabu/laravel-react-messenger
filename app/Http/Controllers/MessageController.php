<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Group;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;

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
        $reciver_id = $data['reciver_id'] ?? null;
        $group_id = $data['group_id'] ?? null;
        $files = $data['files'] ?? [];
        $message = Message::create($data);

        $attachments = [];
        if($files){
            foreach($files as $file){
                $directory = 'attachments/' .Str::random(25);
                Storage::makeDirectory($directory);

                $model =[
                    'message_id' => $message->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $file->store($directory, 'public'),
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ];
                $attachment = MessageAtachment::create($model);
                $attachments[] = $attachment;
            }
            $message->attachments = $attachments;
        }
        if($reciver_id){
            Conversation::updateConversationWithMessage($reciver_id, auth()->id(), $message);
        }
        if($group_id){
            Group::updateGroupWithMessage($group_id, $message);
        }

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
