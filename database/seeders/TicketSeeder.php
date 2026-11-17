<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Database\Seeder;

/**
 * Builds realistic demo tickets by going through TicketService, so every
 * ticket also gets consistent comments and activity history.
 */
class TicketSeeder extends Seeder
{
    public function run(TicketService $service): void
    {
        $admin = User::where('email', 'admin@helpdesk.test')->firstOrFail();
        $tech1 = User::where('email', 'tech1@helpdesk.test')->firstOrFail();
        $tech2 = User::where('email', 'tech2@helpdesk.test')->firstOrFail();
        $andi = User::where('email', 'student1@helpdesk.test')->firstOrFail();
        $rina = User::where('email', 'student2@helpdesk.test')->firstOrFail();
        $cat = Category::pluck('id', 'name');

        // [requester, category, subject, priority, technician, final status, comments[]]
        $tickets = [
            [$andi, 'Hardware', 'Monitor PC Lab 2 berkedip terus', 'medium', $tech1, TicketStatus::Closed, ['Kabel VGA sudah diganti.']],
            [$rina, 'Software', 'Microsoft Office minta aktivasi ulang', 'low', $tech2, TicketStatus::Closed, []],
            [$andi, 'Account & Access', 'Tidak bisa login SIAKAD', 'high', $tech2, TicketStatus::Resolved, ['Password sudah direset, silakan cek email kampus.']],
            [$rina, 'Network', 'Wi-Fi perpustakaan sering terputus', 'high', $tech1, TicketStatus::Resolved, ['Access point lantai 2 sudah di-restart dan firmware diperbarui.']],
            [$andi, 'Laboratory Equipment', 'Proyektor Lab Jaringan tidak menyala', 'high', $tech1, TicketStatus::InProgress, ['Lampu proyektor kemungkinan habis, sedang dicek stok.']],
            [$rina, 'Software', 'Android Studio error saat build', 'medium', $tech2, TicketStatus::InProgress, []],
            [$andi, 'Network', 'Kabel LAN meja 14 Lab 1 tidak terdeteksi', 'medium', $tech2, TicketStatus::Open, []],
            [$rina, 'Hardware', 'Keyboard PC 07 beberapa tombol macet', 'low', $tech1, TicketStatus::Open, []],
            [$andi, 'Account & Access', 'Email kampus penuh, tidak bisa menerima pesan', 'medium', null, TicketStatus::Open, []],
            [$rina, 'Laboratory Equipment', 'AC Lab Multimedia bocor di atas meja', 'high', null, TicketStatus::Open, []],
            [$andi, 'Other', 'Permintaan instalasi software statistik (SPSS)', 'low', null, TicketStatus::Open, []],
            [$rina, 'Network', 'Internet lambat di gedung B', 'critical', null, TicketStatus::Open, []],
        ];

        foreach ($tickets as $i => [$requester, $category, $subject, $priority, $technician, $status, $comments]) {
            $ticket = $service->create($requester, [
                'subject' => $subject,
                'description' => "Laporan demo #".($i + 1).": {$subject}. Mohon segera dicek oleh tim IT.",
                'category_id' => $cat[$category],
                'priority' => $priority,
            ]);

            if ($technician) {
                $service->assign($ticket, $admin, $technician);
            }

            $path = [TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed];
            foreach ($path as $step) {
                if ($this->rank($step) > $this->rank($status)) {
                    break;
                }

                if ($step === TicketStatus::Resolved) {
                    foreach ($comments as $message) {
                        $service->addComment($ticket, $technician, $message);
                    }
                }

                $actor = $step === TicketStatus::Closed ? $requester : $technician;
                $service->changeStatus($ticket, $actor, $step);
            }

            if ($status === TicketStatus::InProgress) {
                foreach ($comments as $message) {
                    $service->addComment($ticket, $technician, $message);
                }
            }
        }

        // One requester follow-up to show a conversation.
        $proyektor = $andi->tickets()->where('subject', 'like', 'Proyektor%')->first();
        $service->addComment($proyektor, $andi, 'Terima kasih, kelas praktikum besok pagi butuh proyektor ini.');
        $service->changePriority($proyektor, $tech1, TicketPriority::Critical);
    }

    private function rank(TicketStatus $status): int
    {
        return array_search($status, TicketStatus::cases(), true);
    }
}
