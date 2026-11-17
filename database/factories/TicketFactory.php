<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_number' => 'TCK-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'user_id' => User::factory(),
            'technician_id' => null,
            'category_id' => Category::factory(),
            'subject' => rtrim(fake()->sentence(5), '.'),
            'description' => fake()->paragraph(3),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'status' => TicketStatus::Open,
        ];
    }

    public function assignedTo(User $technician): static
    {
        return $this->state(['technician_id' => $technician->id]);
    }

    public function status(TicketStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'resolved_at' => in_array($status, [TicketStatus::Resolved, TicketStatus::Closed], true) ? now() : null,
            'closed_at' => $status === TicketStatus::Closed ? now() : null,
        ]);
    }
}
