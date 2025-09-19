<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('date');
            $table->string('time_slot');
            $table->string('status')->default('PENDING');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes to aid availability checks
            $table->index(['employee_id', 'date', 'time_slot']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};

