<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Group;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id1', 'user_id2', 'last_message_id', 'conversation_id', 'group_id'];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id1');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id2');
    }

    public static function getConversationsForSidebar(User $user){
        $users = User::getUserExceptUser($user);
        $groups = Group::getGroupsForUser($user);

        return $users->map(function (User $user){
            return $user->toConversationArray();
        })->concat($groups->map(function (Group $group){
            return $group->toConversationArray();
        }));
    }

    public static function updateConversationWithMessage($conversation, $message)
    {
        // Make sure we have the message ID, not the whole message object
        $messageId = is_object($message) ? $message->id : $message;
        
        return $conversation->update([
            'last_message_id' => $messageId,
            'updated_at' => now()
        ]);
    } 
}
