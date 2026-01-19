<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log; // <-- Tambahkan ini

class TestScheduler extends Command
{
    // ... bagian lain biarkan saja

    // Ubah signature menjadi nama yang mudah dipanggil
    protected $signature = 'test:scheduler';

    // Beri deskripsi
    protected $description = 'Command untuk mengetes scheduler crontab';

    public function handle()
    {
        // Perintah ini akan menulis log setiap kali dijalankan
        Log::info('Scheduler Berhasil Dijalankan oleh Cron!');

        // Optional: tampilkan pesan di konsol jika dijalankan manual
        $this->info('Test log has been written.');
        return 0;
    }
}