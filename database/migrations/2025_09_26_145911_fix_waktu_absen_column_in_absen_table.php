
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom waktu_absen agar tidak auto-update
        DB::statement("
            ALTER TABLE absen 
            MODIFY COLUMN waktu_absen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ");
    }

    public function down(): void
    {
        // Kembalikan ke kondisi awal (kalau rollback)
        DB::statement("
            ALTER TABLE absen 
            MODIFY COLUMN waktu_absen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ");
    }
};
