<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetQueueAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:reset-attempts 
                            {--failed : Hanya reset yang attempts >= 255} 
                            {--delete : Hapus job yang attempts >= 255}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset atau hapus attempts pada tabel jobs agar tidak error Out of Range';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('delete')) {
            $deleted = DB::table('jobs')->where('attempts', '>=', 255)->delete();
            $this->warn("🗑️ Dihapus {$deleted} job(s) yang attempts >= 255.");
            return;
        }

        if ($this->option('failed')) {
            $affected = DB::table('jobs')->where('attempts', '>=', 255)->update(['attempts' => 0]);
            $this->info("✅ Reset attempts pada {$affected} job(s) yang gagal.");
        } else {
            $affected = DB::table('jobs')->update(['attempts' => 0]);
            $this->info("✅ Reset attempts pada {$affected} job(s) total.");
        }
    }
}
