<?php

namespace App\Console\Commands\Benchmarks\CountUsingConditionals\Factories;

use App\Console\Commands\Benchmarks\CountUsingConditionals\Models\Ticket;
use App\Console\Commands\Benchmarks\CountUsingConditionals\Util\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(TicketStatus::cases()),
        ];
    }
}
