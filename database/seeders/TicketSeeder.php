<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Builds realistic demo tickets by going through TicketService, so every
 * ticket also gets consistent comments and activity history.
 * Tickets are spread over the last ~2 months (time is "travelled" with
 * Carbon::setTestNow) so the Reports charts have meaningful data.
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

        // [requester, category, subject, priority, technician, final status, days ago, hours per step, comments[]]
        $tickets = [
            [$andi, 'Hardware', 'Monitor PC Lab 2 berkedip terus', 'medium', $tech1, TicketStatus::Closed, 58, 20, ['Kabel VGA sudah diganti.']],
            [$rina, 'Software', 'Microsoft Office minta aktivasi ulang', 'low', $tech2, TicketStatus::Closed, 55, 30, []],
            [$andi, 'Network', 'Wi-Fi gedung A tidak bisa login portal', 'high', $tech1, TicketStatus::Closed, 50, 4, ['Server captive portal sudah di-restart.']],
            [$rina, 'Account & Access', 'Lupa password e-learning', 'medium', $tech2, TicketStatus::Closed, 46, 2, []],
            [$andi, 'Laboratory Equipment', 'Mouse Lab 3 banyak yang rusak', 'low', $tech2, TicketStatus::Closed, 41, 48, ['10 mouse baru sudah dipasang.']],
            [$rina, 'Hardware', 'Printer lantai 1 paper jam', 'medium', $tech1, TicketStatus::Closed, 37, 6, []],
            [$andi, 'Software', 'Python tidak terinstal di PC Lab 1', 'high', $tech2, TicketStatus::Closed, 33, 12, ['Python 3.12 sudah diinstal di semua PC.']],
            [$rina, 'Network', 'Internet putus total di Lab Jaringan', 'critical', $tech1, TicketStatus::Resolved, 28, 3, ['Switch utama mati, sudah diganti unit cadangan.']],
            [$andi, 'Account & Access', 'Tidak bisa login SIAKAD', 'high', $tech2, TicketStatus::Resolved, 22, 5, ['Password sudah direset, silakan cek email kampus.']],
            [$rina, 'Network', 'Wi-Fi perpustakaan sering terputus', 'high', $tech1, TicketStatus::Resolved, 16, 10, ['Access point lantai 2 sudah di-restart dan firmware diperbarui.']],
            [$andi, 'Other', 'Permintaan akun VPN untuk penelitian', 'low', $tech2, TicketStatus::Resolved, 12, 24, []],
            [$rina, 'Hardware', 'PC dosen di ruang 204 tidak menyala', 'high', $tech1, TicketStatus::Resolved, 8, 6, ['Power supply rusak, sudah diganti.']],
            [$andi, 'Laboratory Equipment', 'Proyektor Lab Jaringan tidak menyala', 'high', $tech1, TicketStatus::InProgress, 5, 8, ['Lampu proyektor kemungkinan habis, sedang dicek stok.']],
            [$rina, 'Software', 'Android Studio error saat build', 'medium', $tech2, TicketStatus::InProgress, 4, 6, []],
            [$andi, 'Network', 'Kabel LAN meja 14 Lab 1 tidak terdeteksi', 'medium', $tech2, TicketStatus::Open, 3, 0, []],
            [$rina, 'Hardware', 'Keyboard PC 07 beberapa tombol macet', 'low', $tech1, TicketStatus::Open, 2, 0, []],
            [$andi, 'Account & Access', 'Email kampus penuh, tidak bisa menerima pesan', 'medium', null, TicketStatus::Open, 2, 0, []],
            [$rina, 'Laboratory Equipment', 'AC Lab Multimedia bocor di atas meja', 'high', null, TicketStatus::Open, 1, 0, []],
            [$andi, 'Other', 'Permintaan instalasi software statistik (SPSS)', 'low', null, TicketStatus::Open, 1, 0, []],
            [$rina, 'Network', 'Internet lambat di gedung B', 'critical', null, TicketStatus::Open, 0, 0, []],
        ];

        $realNow = now();

        try {
            foreach ($tickets as $i => [$requester, $category, $subject, $priority, $technician, $status, $daysAgo, $stepHours, $comments]) {
                $clock = $realNow->copy()->subDays($daysAgo)->setTime(8 + ($i % 8), ($i * 7) % 60);
                $at = function (float $hours = 0) use (&$clock, $realNow) {
                    $clock = $clock->copy()->addMinutes((int) ($hours * 60));
                    Carbon::setTestNow($clock->min($realNow));
                };

                $at();
                $ticket = $service->create($requester, [
                    'subject' => $subject,
                    'description' => 'Laporan demo #'.($i + 1).": {$subject}. Mohon segera dicek oleh tim IT.",
                    'category_id' => $cat[$category],
                    'priority' => $priority,
                ]);

                if (! $technician) {
                    continue;
                }

                $at(0.5);
                $service->assign($ticket, $admin, $technician);

                foreach ([TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed] as $step) {
                    if ($this->rank($step) > $this->rank($status)) {
                        break;
                    }

                    if ($step === TicketStatus::Resolved) {
                        foreach ($comments as $message) {
                            $at($stepHours / 2);
                            $service->addComment($ticket, $technician, $message);
                        }
                    }

                    $at($step === TicketStatus::Closed ? 18 : $stepHours / 2);
                    $service->changeStatus($ticket, $step === TicketStatus::Closed ? $requester : $technician, $step);
                }

                if ($status === TicketStatus::InProgress) {
                    foreach ($comments as $message) {
                        $at(1);
                        $service->addComment($ticket, $technician, $message);
                    }
                }
            }

            // A short conversation + priority escalation on the projector ticket.
            $proyektor = $andi->tickets()->where('subject', 'like', 'Proyektor%')->firstOrFail();
            Carbon::setTestNow($realNow->copy()->subDays(4));
            $service->addComment($proyektor, $andi, 'Terima kasih, kelas praktikum besok pagi butuh proyektor ini.');
            $service->changePriority($proyektor, $tech1, TicketPriority::Critical);
        } finally {
            Carbon::setTestNow();
        }
    }

    private function rank(TicketStatus $status): int
    {
        return array_search($status, TicketStatus::cases(), true);
    }
}
