<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ensure no NULL or empty phone remains before adding unique constraint
        DB::table('employees')->whereNull('phone')->orWhere('phone', '')->update(['phone' => DB::raw("CONCAT('TEMP-', id)")]);

        // Shrink to VARCHAR(20) and keep NOT NULL
        DB::statement("ALTER TABLE employees MODIFY phone VARCHAR(20) NOT NULL");

        // Add unique index on phone
        DB::statement("ALTER TABLE employees ADD UNIQUE KEY employees_phone_unique (phone)");
    }

    public function down(): void
    {
        // Drop unique index, expand length back to 255 (keeping NOT NULL)
        DB::statement("ALTER TABLE employees DROP INDEX employees_phone_unique");
        DB::statement("ALTER TABLE employees MODIFY phone VARCHAR(255) NOT NULL");
    }
};

