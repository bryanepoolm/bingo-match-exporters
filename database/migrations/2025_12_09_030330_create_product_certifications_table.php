<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('certification_id')->constrained('certifications')->cascadeOnDelete();
            $table->string('certificate_number')->nullable();
            $table->string('issued_by')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('document_url')->nullable();
            $table->string('status')->default('valid'); // valid, expired, pending
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_certifications');
    }
};
