<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Message;
use App\Models\Conversation;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'is_admin'=> true
        ]);

        User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false
        ]);

        $users = User::factory(10)->create();

        if ($users->count() > 0) {
            echo "Users created: " . $users->pluck('id')->toJson() . "\n";
        } else {
            echo "No users created.\n";
        }

        for ($i=0; $i < 5; $i++) { 
            $group = Group::factory()->create([
                'owner_id' => 1,
            ]);

            $users = User::inRandomOrder()->limit(rand(2, 5))->pluck('id');

            $group->users()->attach(array_unique([1,...$users]));
        }

        Message::factory(1000)->create();

        echo "Messages created.\n";

        $messages = Message::whereNull('group_id')->orderBy('created_at')->get();

    $conversations = $messages->groupBy(function ($message) {
        return collect([$message->sender_id, $message->reciver_id])->sort()->implode('_');
    })->map(function ($group) {
        return [
            'user_id1' => $group->first()->sender_id,
            'user_id2' => $group->first()->reciver_id,
            'last_message_id' => $group->last()->id,
        ];
    })->values();

    Conversation::insertOrIgnore($conversations->toArray());
    }
}
