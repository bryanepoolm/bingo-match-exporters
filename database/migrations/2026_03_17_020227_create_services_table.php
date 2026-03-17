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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exporter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->nullable()->default('USD');
            
            $table->string('weight_limit')->nullable(); // e.g., "Up to 20 Tons"
            $table->json('destinations')->nullable(); // Array of cities/countries covered
            
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
