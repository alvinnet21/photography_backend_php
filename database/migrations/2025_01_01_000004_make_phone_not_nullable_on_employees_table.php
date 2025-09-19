<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Backfill null values to avoid NOT NULL constraint failure
        DB::table('employees')->whereNull('phone')->update(['phone' => '']);
        // Make column NOT NULL (MySQL)
        DB::statement("ALTER TABLE employees MODIFY phone VARCHAR(255) NOT NULL");
    }

    public function down(): void
    {
        // Revert to NULLABLE (MySQL)
        DB::statement("ALTER TABLE employees MODIFY phone VARCHAR(255) NULL");
    }
};

