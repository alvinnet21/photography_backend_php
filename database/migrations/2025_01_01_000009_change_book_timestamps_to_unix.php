<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add temporary BIGINT columns
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('created_at_int')->nullable();
            $table->unsignedBigInteger('updated_at_int')->nullable();
            $table->unsignedBigInteger('deleted_at_int')->nullable();
        });

        // Backfill from existing DATETIME/TIMESTAMP columns
        DB::statement(
            "UPDATE books SET " .
            "created_at_int = UNIX_TIMESTAMP(created_at), " .
            "updated_at_int = UNIX_TIMESTAMP(updated_at), " .
            "deleted_at_int = IFNULL(UNIX_TIMESTAMP(deleted_at), NULL)"
        );

        // Drop original columns and rename temp columns to original names
        DB::statement("ALTER TABLE books DROP COLUMN created_at, DROP COLUMN updated_at, DROP COLUMN deleted_at");
        DB::statement(
            "ALTER TABLE books " .
            "CHANGE created_at_int created_at BIGINT UNSIGNED NOT NULL, " .
            "CHANGE updated_at_int updated_at BIGINT UNSIGNED NOT NULL, " .
            "CHANGE deleted_at_int deleted_at BIGINT UNSIGNED NULL"
        );
    }

    public function down(): void
    {
        // Add temporary DATETIME columns
        Schema::table('books', function (Blueprint $table) {
            $table->dateTime('created_at_dt')->nullable();
            $table->dateTime('updated_at_dt')->nullable();
            $table->dateTime('deleted_at_dt')->nullable();
        });

        // Backfill from BIGINT unix timestamps
        DB::statement(
            "UPDATE books SET " .
            "created_at_dt = FROM_UNIXTIME(created_at), " .
            "updated_at_dt = FROM_UNIXTIME(updated_at), " .
            "deleted_at_dt = IFNULL(FROM_UNIXTIME(deleted_at), NULL)"
        );

        // Drop BIGINT columns and rename back to original names
        DB::statement("ALTER TABLE books DROP COLUMN created_at, DROP COLUMN updated_at, DROP COLUMN deleted_at");
        DB::statement(
            "ALTER TABLE books " .
            "CHANGE created_at_dt created_at DATETIME NOT NULL, " .
            "CHANGE updated_at_dt updated_at DATETIME NOT NULL, " .
            "CHANGE deleted_at_dt deleted_at DATETIME NULL"
        );
    }
};

