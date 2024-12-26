<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Group;

class Conversation extends Model
{
    use HasFactory;

    protected $filable = ['user_id1', 'user_id2', 'last_message_id'];

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

    public static function updateConversationWithMessage($userId1, $userId2, $messageId){
        $conversation = self::where('user_id1', $userId1)
            ->where('user_id2', $userId2)
            ->orWhere('user_id1', $userId2)
            ->where('user_id2', $userId1)
            ->first();

        if($conversation){
            $conversation->update([
                'last_message_id' => $messageId,
            ]);
        }else{
            self::create([
                'user_id1' => $userId1,
                'user_id2' => $userId2,
                'last_message_id' => $messageId,
            ]);
        }
    } 
}
