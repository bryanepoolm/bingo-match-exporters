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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_id')->constrained('producers')->onDelete('cascade');
            $table->foreignId('exporter_id')->constrained('exporters')->onDelete('cascade');
            
            // Request Details
            $table->string('origin');
            $table->string('destination');
            $table->date('tentative_date');
            $table->text('message')->nullable();
            $table->json('products')->nullable(); // IDs of selected products
            
            $table->float('score')->default(0);
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
