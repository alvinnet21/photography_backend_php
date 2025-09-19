<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Map previous hour-based slots to MORNING/AFTERNOON
        DB::table('books')
            ->whereIn('time_slot', ['09:00', '11:00'])
            ->update(['time_slot' => 'MORNING']);

        DB::table('books')
            ->whereIn('time_slot', ['13:00', '15:00'])
            ->update(['time_slot' => 'AFTERNOON']);
    }

    public function down(): void
    {
        // Cannot reliably revert to exact previous hours; default MORNING -> '09:00', AFTERNOON -> '13:00'
        DB::table('books')->where('time_slot', 'MORNING')->update(['time_slot' => '09:00']);
        DB::table('books')->where('time_slot', 'AFTERNOON')->update(['time_slot' => '13:00']);
    }
};

