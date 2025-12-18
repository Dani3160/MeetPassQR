<?php

namespace Database\Factories;

use App\Models\Attendee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendeeFactory extends Factory
{
    protected $model = Attendee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'ticket_number' => 'TICKET' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'event_name' => $this->faker->words(3, true) . ' Event',
            'qr_code' => md5($this->faker->unique()->email() . time()),
            'is_validated' => false,
        ];
    }
}

