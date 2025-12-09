<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('match_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users'); // Nullable if system change
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_timelines');
    }
};
