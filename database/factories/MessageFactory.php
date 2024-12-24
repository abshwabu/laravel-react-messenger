<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Group;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderId = $this->faker->randomElement([0, 1]);
        $senderId = $this->faker->boolean() ? \App\Models\User::where('id', '!=', 1)->inRandomOrder()->pluck('id')->first() : 1;
        $reciverId = $senderId === 1 ? \App\Models\User::where('id', '!=', 1)->inRandomOrder()->pluck('id')->first() : 1;

        $groupId = null;
        if ($this->faker->boolean(50)) {
            $groupId = $this->faker->randomElement(\App\Models\Group::pluck('id')->toArray());
            $group = Group::find($groupId);
            $senderId = $this->faker->randomElement(\App\Models\Group::find($groupId)->users()->pluck('id')->toArray());
            $reciverId = null;
        } 
        
        return [
            'sender_id' => $senderId,
            'reciver_id' => $reciverId,
            'group_id'=> $groupId,
            'message' => $this->faker->realText(200),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }
}
