<?php

namespace Database\Seeders;

use App\Models\Attendee;
use Illuminate\Database\Seeder;

class AttendeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleAttendees = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '081234567890',
                'ticket_number' => 'TICKET001',
                'event_name' => 'Tech Conference 2024',
                'qr_code' => md5('john.doe@example.com' . 'TICKET001'),
                'is_validated' => false,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '081234567891',
                'ticket_number' => 'TICKET002',
                'event_name' => 'Tech Conference 2024',
                'qr_code' => md5('jane.smith@example.com' . 'TICKET002'),
                'is_validated' => false,
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
                'phone' => '081234567892',
                'ticket_number' => 'TICKET003',
                'event_name' => 'Tech Conference 2024',
                'qr_code' => md5('bob.johnson@example.com' . 'TICKET003'),
                'is_validated' => false,
            ],
        ];

        foreach ($sampleAttendees as $attendee) {
            Attendee::create($attendee);
        }
    }
}

