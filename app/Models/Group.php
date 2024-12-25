<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Message;

class Group extends Model
{
    Use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id', 'last_message_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    // public function lastMessage()
    // {
    //     return $this->belongsTo(Message::class);
    // }
}